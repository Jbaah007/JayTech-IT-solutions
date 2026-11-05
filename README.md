# JayTech IT Solutions

A custom WordPress website for **JayTech IT Solutions**, an IT services company offering technical support, cloud solutions, networking, and web development. The project consists of a custom classic theme and two plugins that add a customer support ticket system, a customer dashboard, and appointment booking.

## Repository structure

```
jaytech-it-solutions/
├── themes/
│   └── jaytech-theme/        Custom classic theme
├── plugins/
│   ├── jaytech-support/      Support tickets and customer dashboard
│   └── jaytech-booking/      Appointment booking
└── docs/
    └── screenshots/          Add screenshots here
```

Only the custom source code is included. WordPress core, `wp-config.php`, uploads, and database exports are intentionally excluded.

## Features

### Theme: `jaytech-theme`
- Responsive navy, white, and teal design with subtle scroll animations
- Homepage with hero, introduction, service cards, "why choose us" section, and calls to action
- Page templates: About Us, Services, Contact Us, plus a default page layout
- Contact form with nonce, validation, honeypot, and rate limiting
- Header and footer menus with an automatic fallback that links only to published pages
- Homepage text and contact details editable in the Customizer
- Accessible navigation (skip link, `aria-expanded`, visible focus styles, reduced-motion support)

### Plugin: `jaytech-support`
- `jt_ticket` custom post type with an admin interface
- Frontend ticket form (subject, description, category, priority) for logged-in users
- Statuses: Open, In Progress, Resolved (set by administrators only)
- Customer dashboard with ticket totals, a ticket table, and ticket detail pages
- Customers can only see their own tickets

### Plugin: `jaytech-booking`
- `jt_appointment` custom post type, managed under **Appointments** in wp-admin
- Booking form for logged-in users with service, date, time, and request details
- Past-date and duplicate-slot prevention, using the site timezone
- Configurable duration and business hours (**Appointments → Settings**)
- Statuses: Pending, Confirmed, Completed, Cancelled
- Customers can view their own appointments and statuses

### Security
Nonces on every state-changing request, server-side validation, sanitization on input, escaping on output, capability checks, ownership checks, and no custom SQL (WordPress APIs only).

## Shortcodes

| Shortcode | Purpose | Suggested page slug |
|---|---|---|
| `[jaytech_support_form]` | Submit and list support tickets | `support-tickets` |
| `[jaytech_customer_dashboard]` | Ticket statistics, table, and details | `customer-dashboard` |
| `[jaytech_booking_form]` | Book and view appointments | `book-appointment` |

## Technologies

PHP 7.4+, WordPress 6.3+, MySQL/MariaDB, HTML5, CSS3 (custom properties, grid), vanilla JavaScript.

## Installation

1. Copy `themes/jaytech-theme` into `wp-content/themes/`.
2. Copy `plugins/jaytech-support` and `plugins/jaytech-booking` into `wp-content/plugins/`.
3. In wp-admin, activate the theme (**Appearance → Themes**) and both plugins (**Plugins**). Activate the plugins through the Plugins screen so administrators receive the ticket and appointment permissions.
4. Create these pages (**Pages → Add New**) with the exact slugs and publish them:
   - **Support Tickets** (`support-tickets`) containing `[jaytech_support_form]`
   - **Customer Dashboard** (`customer-dashboard`) containing `[jaytech_customer_dashboard]`
   - **Book Appointment** (`book-appointment`) containing `[jaytech_booking_form]`
   - Optional: **About Us** (`about-us`), **Services** (`services`), **Contact** (`contact`)
5. Set your timezone under **Settings → General** (booking hours depend on it).
6. Under **Appearance → Customize**, edit **JayTech Homepage** and **JayTech Contact Details** (phone, email, address, hours). The defaults are placeholders.
7. Optionally set the appointment length and business hours under **Appointments → Settings**.
8. Customers need a WordPress account to submit tickets or book appointments. Enable registration under **Settings → General** if desired.

The site works with any permalink structure, but "Post name" is recommended.

## Screenshots

Add images to `docs/screenshots/` and reference them here, for example:

```markdown
![Homepage](docs/screenshots/homepage.png)
![Customer dashboard](docs/screenshots/dashboard.png)
![Appointment booking](docs/screenshots/booking.png)
```

Do not include screenshots that show real customer data.

## Contact form email

The contact form emails the address set in **Customize → JayTech Contact Details**, falling back to the site admin email. Mail delivery depends on your host; consider an SMTP service for production.

## Known limitations

- Tickets are one-way: administrators change the status but cannot reply inside the system.
- No email notifications for tickets or appointments.
- Customers cannot cancel appointments themselves.

## License

GPL-2.0-or-later, matching the plugin and theme headers. Add a `LICENSE` file with the full GPL v2 text before publishing.
