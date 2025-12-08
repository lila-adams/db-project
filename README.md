# 📚 Comic Tracker - Your Personal Comic Book Management System

A beautiful, modern web application for tracking, organizing, and sharing your comic book collection.

## ✨ Features

### Comic Management
- 📖 Add new comics with detailed information
- ⭐ Rate comics on a 0-10 scale
- 📊 Track reading status (New/Reading/Complete)
- ✍️ Write and edit personal reviews
- 🏷️ Tag comics for organization
- 🔍 Search and filter your collection

### Author & Artist Tracking
- 👤 Link comics to their authors
- 🎨 Link comics to their artists
- 📚 View all comics by specific author/artist
- 🔗 Manage author/artist relationships

### Social Features
- 👥 Browse other users' profiles
- 📖 View other users' reading goals
- 💡 See others' recommendations
- 🎯 Share your own goals and recommendations

### User Profile
- 🎯 Set and track reading goals
- 💡 Create recommendation lists
- 👤 Customize your profile
- 📋 View your collection statistics

## 🚀 Quick Start

### Prerequisites
- PHP 7.4+
- MySQL / MariaDB
- XAMPP (recommended for Windows)

### Installation

1. **Start Database**
   ```powershell
   # Ensure MySQL is running in XAMPP or your MySQL server is started
   ```

2. **Start Web Server**
   ```powershell
   cd c:\xampp\htdocs\db-project
   php -S localhost:8000 -t public_html
   ```

3. **Access Application**
   ```
   http://localhost:8000
   ```

4. **Create Account**
   - Click "Sign Up"
   - Create your username and password
   - Start tracking your comics!

## 📖 Documentation

- **[QUICKSTART.md](QUICKSTART.md)** - Setup and usage guide
- **[APP_DOCUMENTATION.md](APP_DOCUMENTATION.md)** - Technical reference
- **[UI_GUIDE.md](UI_GUIDE.md)** - Visual design guide
- **[CHECKLIST.md](CHECKLIST.md)** - Complete feature checklist
- **[SUMMARY.md](SUMMARY.md)** - Project overview

## 🎨 Design

- **Color Scheme**: Modern purple gradient
- **Layout**: Responsive, mobile-first design
- **Components**: Clean card-based interface
- **Icons**: Emoji-based visual indicators
- **Animation**: Smooth transitions and hover effects

## 🔐 Security

- ✅ Password hashing (PHP password_hash)
- ✅ SQL injection prevention (prepared statements)
- ✅ Session-based authentication
- ✅ User ownership validation
- ✅ Input sanitization and validation

## 📁 Project Structure

```
public_html/
├── Authentication
│   ├── login.php
│   ├── signup.php
│   └── logout.php
├── Dashboard & Management
│   ├── dashboard.php
│   ├── entry-detail.php
│   ├── update-entry.php
│   └── profile.php
├── Discovery
│   ├── browse-profiles.php
│   ├── author-entries.php
│   └── artist-entries.php
├── Database Functions
│   ├── connect-db.php
│   ├── user-db.php
│   ├── entry-db.php
│   ├── author-db.php
│   ├── artist-db.php
│   ├── tag-db.php
│   ├── goal-db.php
│   └── rec-db.php
└── Configuration
    └── (Database credentials in connect-db.php)
```

## 💾 Database

**Local Configuration:**
- Host: `127.0.0.1`
- Port: `3306`
- Database: `comic-proj-db`
- User: `nemo`
- Password: `Nemo2468&`

See [QUICKSTART.md](QUICKSTART.md) for table schemas.

## 📱 Pages Overview

| Page | Purpose |
|------|---------|
| login.php | User authentication |
| signup.php | Account creation |
| dashboard.php | Main hub for managing comics |
| entry-detail.php | View full comic details |
| update-entry.php | Edit comic information |
| profile.php | Manage goals and recommendations |
| browse-profiles.php | Discover other users |
| author-entries.php | Comics by specific author |
| artist-entries.php | Comics by specific artist |

## 🎯 Core Functions

### Entry Management
```php
addEntry($comic_name, $rating, $user_id, $curr_status, $review)
getAllEntries($user_id)
getEntryById($entry_id)
updateEntry($entry_id, $comic_name, $rating, $user_id, $curr_status, $review)
deleteEntry($entry_id)
```

### Author Management
```php
addAuthor($name)
getEntriesByAuthor($author_id, $user_id)
```

### Artist Management
```php
addArtist($name)
getEntriesByArtist($artist_id, $user_id)
```

### Goals & Recommendations
```php
addGoal($user_id, $text)
addRec($comic_name, $user_id)
getAllGoals($user_id)
getAllRecommendations($user_id)
```

## 🎓 Usage Example

### Adding a Comic
1. Go to Dashboard
2. Fill the "Add New Entry" form
3. Enter comic details (name, rating, status, review)
4. Click "Add Comic"
5. Comic appears in your list

### Viewing Comic Details
1. Click on comic name in dashboard
2. See full details, rating, review, authors/artists
3. Click "Edit" to modify or "Back" to return

### Setting Goals
1. Go to "My Profile"
2. Add reading goals like "Read 50 comics this year"
3. Share with others in "Browse Profiles"

### Sharing Recommendations
1. Go to "My Profile"
2. Add comics you recommend to others
3. Others can see your recommendations

## 🔧 Troubleshooting

### Database Connection Error
- Ensure MySQL is running
- Check credentials in `connect-db.php`
- Verify database `comic-proj-db` exists

### Can't Login
- Verify you created an account
- Check password is correct
- Try clearing browser cache

### Missing Tables
- Run SQL schema (see QUICKSTART.md)
- Ensure all tables are created
- Verify user has permissions

## 🚀 Deployment

To deploy to a web server:
1. Upload files to web directory
2. Update database credentials in `connect-db.php`
3. Run database schema creation script
4. Access through your domain

## 📝 License

This project is provided as-is for educational purposes.

## 🎉 Enjoy!

Start tracking your comic collection today! 📚✨

---

**Version:** 1.0  
**Status:** Complete & Ready to Use  
**Last Updated:** December 2025

For detailed setup, see [QUICKSTART.md](QUICKSTART.md)  
For API reference, see [APP_DOCUMENTATION.md](APP_DOCUMENTATION.md)
