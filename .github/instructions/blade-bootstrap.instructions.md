---
applyTo: "resources/views/**/*.blade.php,resources/css/**/*.css,resources/js/**/*.js"
description: "Use when editing Blade templates, Bootstrap 5 layouts, Laravel frontend assets, or server-rendered UI for this project."
---

# Blade And Bootstrap Instructions

- Use Blade templates and Bootstrap 5 components as the default UI approach.
- Preserve the current warm, club-oriented visual style unless a redesign is explicitly requested.
- Prefer semantic HTML and accessible form markup.
- Use Bootstrap utility classes before adding custom CSS, but add custom CSS when needed to keep the UI intentional.
- Keep forms straightforward and server-rendered.
- Avoid frontend framework dependencies unless explicitly requested.
- Keep JavaScript minimal and only use it where Bootstrap behavior needs it.
- When adding new screens, make them work on desktop and mobile.

For this project specifically:
- Result entry, edit, and overview pages should stay easy to scan during training use.
- Favor clarity over flashy interactions.
- Use consistent labels for date, discipline, score, and note fields.