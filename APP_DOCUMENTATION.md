# Comic Tracker App - Complete Documentation

## 📚 Overview
A full-featured PHP/MySQL web application for tracking, organizing, and sharing comic book collections. Users can manage their comics, rate them, track reading status, and share goals and recommendations with the community.

## 🎨 Aesthetic Design
- **Color Scheme**: Purple gradient (Primary: #667eea, Secondary: #764ba2)
- **Modern UI**: Rounded corners, smooth shadows, glassmorphism effects
- **Responsive**: Works seamlessly on desktop and mobile devices
- **Minimal & Clean**: Focuses on content with distraction-free design

---

## 🔧 Backend Setup

### Database Connection
- **File**: `connect-db.php`
- **Local Setup**:
  - Host: `127.0.0.1`
  - Port: `3306`
  - Database: `comic-proj-db`
  - Username: `nemo`
  - Password: `Nemo2468&`

### Running the Application Locally
```powershell
php -S localhost:8000 -t public_html
```
Then navigate to: `http://localhost:8000`

---

## 📋 Database Functions

### User Management (`user-db.php`)
- `addUser($username, $pwd)` - Register new user
- `getAllUsers()` - Fetch all users
- `delete_user($user_id)` - Delete user account
- `updateUser($user_id, $username, $pwd)` - Update user info

### Entry Management (`entry-db.php`)
- `addEntry($comic_name, $rating, $user_id, $curr_status, $review)` - Add new comic
- `getAllEntries($user_id)` - Get user's all comics
- `getEntryById($entry_id)` - Get specific entry details
- `updateEntry($entry_id, $comic_name, $rating, $user_id, $curr_status, $review)` - Update comic
- `deleteEntry($entry_id)` - Delete comic and associated tags
- `searchEntries(...)` - Advanced search with filters

### Tags (`tag-db.php`)
- `addNewTagName($tag_text)` - Create new tag
- `addNewTagRelationship($tag_id, $entry_id)` - Link tag to entry
- `getAllTags($entry_id)` - Get tags for an entry
- `delete_tag($entry_id, $tag_id)` - Remove tag from entry
- `getTagByText($tag_text)` - Find tag by name

### Authors (`author-db.php`)
- `addAuthor($name)` - Add new author
- `addAuthorToEntry($entry_id, $author_id, $year)` - Link author to comic
- `getAuthorByEntry($entry_id, $user_id)` - Get author info for comic
- `getEntriesByAuthor($author_id, $user_id)` - Get all comics by author
- `updateAuthor($author_id, $name)` - Update author info
- `deleteWrittenBy($author_id, $entry_id)` - Remove author from comic

### Artists (`artist-db.php`)
- `addArtist($name)` - Add new artist
- `addArtistToEntry($entry_id, $artist_id)` - Link artist to comic
- `getArtistByEntry($entry_id, $user_id)` - Get artist info for comic
- `getEntriesByArtist($artist_id, $user_id)` - Get all comics by artist
- `updateArtist($artist_id, $name)` - Update artist info
- `deleteDrawnBy($artist_id, $entry_id)` - Remove artist from comic

### Goals (`goal-db.php`)
- `addGoal($user_id, $text)` - Create reading goal
- `getAllGoals($user_id)` - Get user's goals
- `delete_goal($goal_id)` - Delete goal
- `updateGoal($goal_id, $text)` - Update goal

### Recommendations (`rec-db.php`)
- `addRec($comic_name, $user_id)` - Add recommendation
- `getAllRecommendations($user_id)` - Get user's recommendations
- `delete_rec($rec_id)` - Delete recommendation
- `updateRec($rec_id, $comic_name)` - Update recommendation

---

## 📄 Page Structure & Routes

### Authentication
- **`login.php`** - User login with password hashing
- **`signup.php`** - New account creation with validation
- **`logout.php`** - Session destruction

### Main Dashboard
- **`dashboard.php`** - Home page after login
  - Add new comic entries
  - View all user's comics
  - Quick delete/edit buttons
  - Search and filter functionality

### Entry Management
- **`entry-detail.php`** - Full comic details page
  - Rating, status, review display
  - Author/artist information
  - Tags visualization
  - Edit and delete options

- **`update-entry.php`** - Edit comic entry form
  - Modify name, rating, status, review
  - Redirect back to detail page after save

### User Profile
- **`profile.php`** - User profile page
  - Reading goals management (add/delete)
  - Recommendations management (add/delete)
  - Profile customization area
  - Empty states with helpful messages

### Discovery & Social
- **`browse-profiles.php`** - Community exploration
  - View all registered users
  - Browse other users' goals
  - Browse other users' recommendations
  - Read-only viewing (can't modify others' data)

### Author & Artist Pages
- **`author-entries.php`** - Filtered view by author
  - Shows all comics user has by specific author
  - Author name at top
  - Quick links back to dashboard

- **`artist-entries.php`** - Filtered view by artist
  - Shows all comics user has by specific artist
  - Artist name at top
  - Quick links back to dashboard

---

## 🔐 Security Features

1. **Session Management**
   - Required login on protected pages
   - Automatic redirect to login if not authenticated
   - User ID stored in `$_SESSION['user_id']`

2. **Password Security**
   - Uses `password_hash()` with `PASSWORD_DEFAULT`
   - Verified with `password_verify()`
   - Never stored in plain text

3. **Database Security**
   - Prepared statements with parameterized queries
   - SQL injection protection
   - PDO exception handling

4. **User Ownership Validation**
   - Entries verified to belong to logged-in user
   - Prevents accessing other users' data
   - Authorization checks on update/delete operations

5. **Input Sanitization**
   - `htmlspecialchars()` for output escaping
   - `trim()` for whitespace handling
   - Form validation on client and server side

---

## 🎯 Core Features

### Add Comic Entry
- Comic name (required)
- Rating (0-10, optional)
- Reading status (New/Reading/Complete)
- Personal review/notes
- Tags (searchable)

### Organize & Filter
- View all comics in dashboard
- Search by name, rating, status
- Filter by author or artist
- Group by reading status

### Social Features
- Share reading goals with others
- Recommend comics to the community
- Browse other users' recommendations
- View reading goals of other users

### Rating System
- 0-10 rating scale
- Visual star display
- Sortable by rating

### Status Tracking
- New: Just added to collection
- Reading: Currently reading
- Complete: Finished

---

## 🎨 UI/UX Components

### Navigation Bar
- Consistent gradient background
- Quick access to dashboard, profile, browse
- Logout button

### Cards & Containers
- White background with shadow effects
- Rounded corners (12px)
- Proper spacing and padding

### Buttons
- Color-coded (primary: purple, danger: red, success: green)
- Hover effects with transitions
- Icon usage for clarity

### Forms
- Clean label styling
- Rounded input fields
- Focus states with colored borders
- Error messages in red

### Status Badges
- Color-coded by status
- Small, uppercase text
- Quick visual scanning

---

## 📊 Data Models

### User Table
- `user_id` (PK)
- `username` (UNIQUE)
- `pwd` (hashed)

### Entry Table
- `entry_id` (PK)
- `comic_name`
- `rating` (0-10)
- `user_id` (FK)
- `curr_status` (new/reading/complete)
- `review`

### Author Table
- `author_id` (PK)
- `name`

### Artist Table
- `artist_id` (PK)
- `name`

### Tag Table
- `tag_id` (PK)
- `entry_id` (FK)

### Tag_Map Table
- `tag_id` (PK)
- `tag_text`

### Goal Table
- `goal_id` (PK)
- `user_id` (FK)
- `text`

### Recommendation Table
- `rec_id` (PK)
- `user_id` (FK)
- `comic_name`

### Written_By (Author-Entry Junction)
- `entry_id` (FK)
- `author_id` (FK)
- `year` (optional)

### Drawn_By (Artist-Entry Junction)
- `entry_id` (FK)
- `artist_id` (FK)

---

## 🚀 Getting Started

1. **Setup Database**
   - Create MySQL database: `comic-proj-db`
   - Create tables with schema (see data models above)
   - Ensure user `nemo` with password `Nemo2468&` has full permissions

2. **Configure Connection**
   - Update `connect-db.php` with your database credentials if different

3. **Start PHP Server**
   ```powershell
   php -S localhost:8000 -t public_html
   ```

4. **Create Account**
   - Navigate to `http://localhost:8000`
   - Click "Sign Up"
   - Create username and password

5. **Start Tracking**
   - Login to dashboard
   - Add your first comic
   - Rate and review
   - Share goals and recommendations

---

## 📁 File Structure
```
public_html/
├── index.php (simple test)
├── connect-db.php (database connection)
├── login.php
├── signup.php
├── logout.php
├── dashboard.php (main hub)
├── profile.php (goals & recs)
├── entry-detail.php (comic details)
├── update-entry.php (edit comic)
├── author-entries.php (comics by author)
├── artist-entries.php (comics by artist)
├── browse-profiles.php (explore community)
├── entry-db.php (entry functions)
├── user-db.php (user functions)
├── author-db.php (author functions)
├── artist-db.php (artist functions)
├── tag-db.php (tag functions)
├── goal-db.php (goal functions)
└── rec-db.php (recommendation functions)
```

---

## 🎓 Future Enhancements
- Image uploads for comic covers
- Advanced search with multiple filters
- Social following/friendship system
- Reading lists & collections
- Review ratings from other users
- Export to CSV/PDF
- Mobile app
- Integration with comic databases (API)

---

**Version**: 1.0  
**Last Updated**: December 2025  
**Status**: Complete & Ready for Use
