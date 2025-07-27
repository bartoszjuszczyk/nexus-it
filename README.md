# Nexus IT Helpdesk System

[![Symfony Version](https://img.shields.io/badge/Symfony-7.x-blueviolet)](https://symfony.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://www.php.net)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

An advanced IT helpdesk and ticketing system built with the Symfony framework. This application provides a complete
solution for managing internal IT support requests, from ticket creation and real-time communication to knowledge base
management and reporting.

***

## ✨ Key Features

### Core Ticketing System

- **Ticket Creation:** Employees can easily submit new support tickets.
- **Ticket Management:** A comprehensive dashboard for IT staff to view, manage, and respond to all tickets.
- **Ticket Assignment:** Assign tickets to specific IT support members.
- **Ticket Priorities:** Set priority levels (Low, Medium, High, etc.) for tickets.
- **File Attachments:** Users can attach files (screenshots, logs) to tickets and comments.
- **Full Ticket History:** A chronological timeline of all events for a ticket (comments, status changes, assignments).

### User & Security Management

- **User Authentication:** Secure login system with a "Remember Me" feature.
- **User Roles:** Pre-defined roles with distinct permissions (Employee, IT Support, Administrator).
- **Two-Factor Authentication (2FA):** Enhanced security for accounts using TOTP apps.
- **Administrator Panel:** Manage user accounts and roles.

### Knowledge Base

- **Article Management:** IT staff can create, edit, and delete knowledge base articles.
- **Category Management:** Organize articles into categories.
- **Public KB View:** A searchable and filterable knowledge base for all employees to find solutions independently.

### Notifications & Communication

- **Email Notifications:** Automated email alerts for key events (new comments, status changes).
- **Real-time Updates:** Live updates for new comments and status changes without a page refresh, powered by **Mercure
  **.

### Reporting & Feedback

- **Statistics Dashboard:** View key metrics like new tickets, average resolution time, and more.
- **Charts:** A visual breakdown of tickets by status.
- **Satisfaction Surveys:** Automated feedback requests sent to users after a ticket is closed.

### Additional Features

- **Asset Management:** A simple inventory system to track company hardware.

---

## 🛠️ Tech Stack

- **Backend:** PHP 8.2+, Symfony 7.x, Doctrine ORM
- **Frontend:** Twig, Stimulus (Symfony UX), SCSS, AssetMapper
- **Real-time:** Symfony UX Turbo, Mercure
- **Key Components:** Symfony Workflow, Symfony Security (2FA), Symfony Messenger
- **Development Environment:** Warden

---
<!-- GitAds-Verify: Y31ZNJVKXGDP9T9YMEAJNPYRVXO9AH38 -->

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
