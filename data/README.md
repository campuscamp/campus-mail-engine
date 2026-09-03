# Data Directory

This directory stores local data files (SQLite databases, JSON exports, etc.).

**All files in this directory are gitignored** except this README and `.gitkeep`.

In production (GitHub Actions), local storage is not persistent between runs.
Use an external storage adapter for production data.
