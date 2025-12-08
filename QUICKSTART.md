# Comic Tracker - Quick Start Guide

## ✅ What's Complete

Your comic tracker app now has:

### 🎯 Core Pages (All Implemented)
- ✅ **Login Page** - Beautiful gradient login with password hashing
- ✅ **Sign Up Page** - New account creation with validation
- ✅ **Dashboard** - Add comics, view all entries, quick edit/delete
- ✅ **Entry Detail Page** - Full comic details with rating, review, author/artist
- ✅ **Update Entry** - Edit comic information and review
- ✅ **My Profile** - Manage reading goals and recommendations
- ✅ **Browse Profiles** - View other users' goals and recommendations
- ✅ **Author Page** - View all your comics by a specific author
- ✅ **Artist Page** - View all your comics by a specific artist

### 🔧 Backend (All Implemented)
- ✅ User authentication with password hashing
- ✅ Entry CRUD operations (Create, Read, Update, Delete)
- ✅ Author management
- ✅ Artist management
- ✅ Tag system for comics
- ✅ Goal tracking system
- ✅ Recommendation sharing
- ✅ Database connection with error handling

### 🎨 Design
- ✅ Consistent purple gradient theme
- ✅ Responsive Bootstrap layout
- ✅ Modern card-based design
- ✅ Smooth transitions and hover effects
- ✅ Emoji icons for visual clarity
- ✅ Mobile-friendly interface

---

## 🚀 Getting Started

### 1. Start Your Database Server
- Open XAMPP Control Panel
- Start "MySQL" module
- (Or ensure your MySQL server is running)

### 2. Start PHP Server
Open PowerShell in your project directory and run:
```powershell
php -S localhost:8000 -t public_html
```

### 3. Access the App
Open your browser and go to:
```
http://localhost:8000
```

### 4. Create Your Account
1. Click **"Sign Up"**
2. Enter username and password
3. Log in with your credentials
4. Start adding comics!

---

## 📱 How to Use

### Adding a Comic
1. Go to **Dashboard**
2. Fill in the form on the left:
   - Comic Name (required)
   - Rating (0-10)
   - Status (New/Reading/Complete)
   - Review/Notes
3. Click **"Add Comic"**

### Viewing Comic Details
1. Click on a comic name in your list
2. See full details including:
   - Rating & status
   - Your review
   - Author & artist info
   - Tags

### Editing a Comic
1. Click on a comic to view details
2. Click **"✏️ Edit Entry"**
3. Modify details as needed
4. Click **"💾 Save Changes"**

### Deleting a Comic
1. From dashboard, click **"🗑️ Delete"** button
2. Confirm deletion

### Managing Goals
1. Go to **My Profile**
2. Add reading goals like "Read 50 comics this year"
3. Delete goals when completed

### Sharing Recommendations
1. Go to **My Profile**
2. Add comics you recommend to others
3. Other users can see your recommendations in **Browse Profiles**

### Viewing Other Users
1. Click **"👥 Browse Profiles"** in navbar
2. Select a user from the list
3. See their goals and recommendations

### Filter by Author/Artist
1. From entry details, click on an author/artist name
2. See all your comics by that person

---

## 🔑 Database Credentials
- **Host**: 127.0.0.1
- **Port**: 3306
- **Database**: comic-proj-db
- **Username**: nemo
- **Password**: Nemo2468&

> Note: Update these in `connect-db.php` if you're using different credentials

---

## 📊 Database Tables You Need

Create these tables in your MySQL database:

### User
```sql
CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    pwd VARCHAR(255) NOT NULL
);
```

### Entry
```sql
CREATE TABLE Entry (
    entry_id INT AUTO_INCREMENT PRIMARY KEY,
    comic_name VARCHAR(255) NOT NULL,
    rating INT,
    user_id INT NOT NULL,
    curr_status VARCHAR(50),
    review TEXT,
    FOREIGN KEY (user_id) REFERENCES User(user_id)
);
```

### Author
```sql
CREATE TABLE Author (
    author_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);
```

### Artist
```sql
CREATE TABLE Artist (
    artist_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);
```

### Goal
```sql
CREATE TABLE Goal (
    goal_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    text TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User(user_id)
);
```

### Recommendation
```sql
CREATE TABLE Recommendation (
    rec_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    comic_name VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User(user_id)
);
```

### Tag_Map
```sql
CREATE TABLE tag_map (
    tag_id INT AUTO_INCREMENT PRIMARY KEY,
    tag_text VARCHAR(255) UNIQUE NOT NULL
);
```

### Tag
```sql
CREATE TABLE tag (
    tag_id INT NOT NULL,
    entry_id INT NOT NULL,
    PRIMARY KEY (tag_id, entry_id),
    FOREIGN KEY (tag_id) REFERENCES tag_map(tag_id),
    FOREIGN KEY (entry_id) REFERENCES Entry(entry_id)
);
```

### Written_By
```sql
CREATE TABLE written_by (
    entry_id INT NOT NULL,
    author_id INT NOT NULL,
    year INT,
    PRIMARY KEY (entry_id, author_id),
    FOREIGN KEY (entry_id) REFERENCES Entry(entry_id),
    FOREIGN KEY (author_id) REFERENCES Author(author_id)
);
```

### Drawn_By
```sql
CREATE TABLE drawn_by (
    entry_id INT NOT NULL,
    artist_id INT NOT NULL,
    PRIMARY KEY (entry_id, artist_id),
    FOREIGN KEY (entry_id) REFERENCES Entry(entry_id),
    FOREIGN KEY (artist_id) REFERENCES Artist(artist_id)
);
```

---

## 🎨 Design Features

**Color Palette:**
- Primary: #667eea (Purple)
- Secondary: #764ba2 (Deep Purple)
- Background: White with gradient overlay
- Accent: Various colors for status (blue=new, orange=reading, green=complete)

**Typography:**
- Clean, modern sans-serif
- Large headings with emoji icons
- Clear hierarchy and spacing

**Interactive Elements:**
- Hover animations on cards
- Smooth button transitions
- Form focus states with color feedback
- Responsive grid layout

---

## 🐛 Troubleshooting

### Can't Connect to Database
- Check MySQL is running
- Verify credentials in `connect-db.php`
- Ensure database `comic-proj-db` exists

### Can't Login
- Make sure you created an account first
- Check password is correct
- Verify user account exists in database

### Pages not found (404)
- Make sure you're running: `php -S localhost:8000 -t public_html`
- Navigate to correct URL: `http://localhost:8000`

### Blank page after action
- Check browser console for errors (F12)
- Check `connect-db.php` database connection
- Verify all `require()` statements point to correct files

---

## 📈 What to Explore Next

1. **Add Author/Artist Info** - Click on an author/artist link to see all comics by them
2. **Share with Others** - Add goals and recommendations to share with the community
3. **Browse Profiles** - Discover what other users are reading

---

## 📚 For Developers

All code is modular and well-organized:
- `*-db.php` files contain database functions
- `login.php` and `signup.php` handle authentication
- `dashboard.php` is the main hub
- Each page is self-contained and includes proper error handling
- Uses prepared statements for security
- Includes form validation and sanitization

---

**Happy Reading!** 📚✨

Version 1.0 | Complete December 2025
