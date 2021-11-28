# Habits 📝

**Description**: Track your habits, one by one. A simple habit tracker application.

**See**: [Video Demo]()

## Contents

- [Why?](#why)
- [Desired Features](#desired-features)
- [UI / UX Design](#ui--ux-design)
- [Tech Stack](#tech-stack)
- [Development Set Up](#development-setup)

## Why?

Habits shape our identities, and ultimately makes us who we are.
I wanted to build a simple, minimal and distraction free habit tracking application.

## Desired Features

Whilst ideating the features for the application, I came up with the following:

- Start habits (set daily/weekly habits)
- Complete habits
- See stats (completion rates)
- Get habit reminders
- Organise Habits

However, as time was limited, I isolated the core functionality that would allow me to build a MVP

- Start daily / weekly habits
- Track habits
- Stop / Edit habits
- Basic authentication flow

## UI / UX Design

For the UI / UX design, I have taken inspiration from the following sources:

- Pixel True Habit Tracker UI - [Figma](https://www.figma.com/file/pb17Z38bRh18K74mVPmXAX/Pixel-True---Habit-Builder-UI-Kit?node-id=0%3A1) ([source](https://www.pixeltrue.com/free-ui-kits/habit-builder-ui-kit))
- Habit Tracker App UI by Hafid Fachrudin - [Dribbble](https://dribbble.com/shots/14233911-Habit-Tracker-App)

## Tech Stack

### Front-end

![React](https://img.shields.io/badge/React-2e2e2e?logo=react)
![TypeScript](https://img.shields.io/badge/TypeScript-2e2e2e?logo=typescript)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-2e2e2e?logo=tailwindcss)
![Capacitor](https://img.shields.io/badge/Capacitor-2e2e2e?logo=capacitor)

This is my first time building a native iOS application, without any experience with native frameworks. There are many cross platform technologies, however, and the main two contestants for me were [React Native](https://reactnative.dev) and [Capacitor JS](https://capacitorjs.com).

After trying both out, I ended up sticking with Capacitor as I found it to be the least restrictive and more flexible choice for me.

### Back-end

![Laravel](https://img.shields.io/badge/Laravel-2e2e2e?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-2e2e2e?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-2e2e2e?logo=mysql)

I have built the RESTful API using Laravel, as it offers everything that you might need for backend development. I have taken this chance to follow DDD (albeit an overkill), and TDD as it provides instant feedback loops along with its many other benefits.

## Development Setup

Please see the `README.md` files in the `client/` and `api/` directories respectively on how to setup up the development environment to run this application.
