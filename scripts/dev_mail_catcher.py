#!/usr/bin/env python3

import asyncio
import email
import json
import os
import uuid
from datetime import datetime, timezone
from email import policy


OUTPUT_DIR = os.path.join(os.path.dirname(__file__), '..', 'storage', 'app', 'dev-mail')


def ensure_output_dir() -> None:
    os.makedirs(OUTPUT_DIR, exist_ok=True)


def extract_bodies(message: email.message.EmailMessage) -> tuple[str | None, str | None]:
    text_body = None
    html_body = None

    if message.is_multipart():
        for part in message.walk():
            if part.get_content_maintype() == 'multipart':
                continue

            content_type = part.get_content_type()
            payload = part.get_payload(decode=True) or b''
            charset = part.get_content_charset() or 'utf-8'
            decoded = payload.decode(charset, errors='replace')

            if content_type == 'text/plain' and text_body is None:
                text_body = decoded

            if content_type == 'text/html' and html_body is None:
                html_body = decoded
    else:
        payload = message.get_payload(decode=True) or b''
        charset = message.get_content_charset() or 'utf-8'
        decoded = payload.decode(charset, errors='replace')

        if message.get_content_type() == 'text/html':
            html_body = decoded
        else:
            text_body = decoded

    return text_body, html_body


def save_message(mail_from: str, rcpt_tos: list[str], data: bytes) -> None:
    ensure_output_dir()

    message = email.message_from_bytes(data, policy=policy.default)
    text_body, html_body = extract_bodies(message)
    captured_at = datetime.now(timezone.utc).isoformat()
    message_id = f"{datetime.now().strftime('%Y%m%d%H%M%S')}-{uuid.uuid4().hex[:8]}"

    payload = {
        'id': message_id,
        'captured_at': captured_at,
        'mail_from': mail_from,
        'rcpt_tos': rcpt_tos,
        'subject': message.get('subject', '(no subject)'),
        'text_body': text_body,
        'html_body': html_body,
        'raw': data.decode('utf-8', errors='replace'),
    }

    output_path = os.path.join(OUTPUT_DIR, f'{message_id}.json')

    with open(output_path, 'w', encoding='utf-8') as handle:
        json.dump(payload, handle, ensure_ascii=False, indent=2)

    print(f'Captured mail: {payload["subject"]} -> {", ".join(rcpt_tos)}', flush=True)


async def handle_client(reader: asyncio.StreamReader, writer: asyncio.StreamWriter) -> None:
    mail_from = ''
    rcpt_tos: list[str] = []

    writer.write(b'220 ClayResults Dev Mail Catcher\r\n')
    await writer.drain()

    while not reader.at_eof():
        line = await reader.readline()

        if not line:
            break

        command = line.decode('utf-8', errors='replace').rstrip('\r\n')
        upper_command = command.upper()

        if upper_command.startswith('EHLO') or upper_command.startswith('HELO'):
            writer.write(b'250-Hello\r\n250 SIZE 52428800\r\n')
        elif upper_command.startswith('MAIL FROM:'):
            mail_from = command[10:].strip().strip('<>')
            rcpt_tos = []
            writer.write(b'250 OK\r\n')
        elif upper_command.startswith('RCPT TO:'):
            rcpt_tos.append(command[8:].strip().strip('<>'))
            writer.write(b'250 OK\r\n')
        elif upper_command == 'DATA':
            writer.write(b'354 End data with <CR><LF>.<CR><LF>\r\n')
            await writer.drain()

            data_lines: list[bytes] = []

            while True:
                data_line = await reader.readline()

                if data_line in {b'.\r\n', b'.\n', b'.'}:
                    break

                if data_line.startswith(b'..'):
                    data_line = data_line[1:]

                data_lines.append(data_line)

            save_message(mail_from, rcpt_tos, b''.join(data_lines))
            writer.write(b'250 Message accepted\r\n')
        elif upper_command == 'RSET':
            mail_from = ''
            rcpt_tos = []
            writer.write(b'250 OK\r\n')
        elif upper_command == 'NOOP':
            writer.write(b'250 OK\r\n')
        elif upper_command == 'QUIT':
            writer.write(b'221 Bye\r\n')
            await writer.drain()
            break
        else:
            writer.write(b'250 OK\r\n')

        await writer.drain()

    writer.close()
    await writer.wait_closed()


async def main() -> None:
    ensure_output_dir()
    server = await asyncio.start_server(handle_client, '127.0.0.1', 1025)
    print('Mail catcher listening on smtp://127.0.0.1:1025', flush=True)
    print('Open http://127.0.0.1:8000/dev/mail to inspect captured emails.', flush=True)

    async with server:
        await server.serve_forever()


if __name__ == '__main__':
    asyncio.run(main())