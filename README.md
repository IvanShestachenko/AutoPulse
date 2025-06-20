# AutoPulse

**AutoPulse** is a small-scale web platform for selling new and used vehicles, developed as a coursework project for the subject **FEL CTU B6B39ZWA – Fundamentals of Web Applications**.

Live deployment is available at:  
[AutoPulse online instance](https://zwa.toad.cz/~shestiva/autopulse/core/index.php)

> A demo user was created for demonstration purposes. The account already contains several listings.
> 
> **Email:** `sales@apexautogroup.cz`  
> **Password:** `mebnwdo-700`

---

## Overview

The project was created without the use of frameworks or third-party libraries — per course constraints — using only **vanilla PHP, JavaScript, HTML, and CSS**. This was done intentionally, as the aim of the course was to get hands-on experience with the low-level mechanisms that frameworks usually abstract away and handle “under the hood.”

The project uses **server-side rendering** (I would have gladly experimented with the REST approach instead) and was built with **MySQL** as the database and tested using **Apache (XAMPP package)**.


The design was mostly inspired by platforms such as **sauto.cz** and similar automotive portals. The project does not contain a whole load of features, while, however, fully satisfying all of the 30+ course requirements — such as:

- different functionality for multiple user roles;  
- correct pagination;  
- AJAX integration;  
- protection against XSS and SQL injections;  
- application of the PRG (Post/Redirect/Get) principle;
- etc.

The triviality of the applied stack, architecture and overall concept is what limits further development of the platform. 
Instead, I would be glad to invest his effort into a more sophisticated project using technologies and tools that are closer to actual commercial production environments.

---

## Repository structure

The root directory of the project contains the following folders:

| Folder               | Description |
|----------------------|-------------|
| `assets/avatar/`            | Stores cropped thumbnail images for listings (currently those belonging to the demo user). |
| `assets/media/`             | Stores full-size images of car listings, including those of the demo user. |
| `assets/logotypes&buttons/` | Stores static UI images (logos, buttons, etc.). |
| `assets/doxygen/html/`      | Contains generated technical documentation files. |
| `assets/demo-db.sql/`           | A script that generates the demo project database with all the tables, containing the demo user and his listings. |
| `core/`              | Contains all PHP and HTML code. Server-side rendering is used, so there is no strict front-end/back-end separation. |
| `scripts/`           | Contains all JavaScript files. |
| `styles/`            | Contains all CSS styles. |

---

## Documentation

Project documentation is available at the links below or through the project's main page:

- 📘 [Technical documentation (Doxygen)](https://zwa.toad.cz/~shestiva/autopulse/core/doxygen/html/index.html)  
- 📙 [User manual (Google Docs)](https://docs.google.com/document/d/1STvj4TltSsuezUdh32gKZRyjmus9_iT-h9owhwQnyis/edit?usp=sharing)

---

## Notes

Development originally took place on the university’s internal GitLab. The current repository represents an imported version with only minor cosmetic changes in a few branches.

---

## Author

**Ivan Shestachenko, 2025, B6B39ZWA @ FEL CTU**
