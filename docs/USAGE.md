# Usage Guide - Belva Bookstore

This guide explains how to use the Belva Bookstore web application effectively.

## Getting Started

### Access the Application
- **Main URL**: `http://localhost/belva/phpweb/src/`
- The landing page displays featured books and general information

### Default Login Credentials

**Administrator Account**:
- Email: `admin@bookstore.com`
- Password: `password`

**User Account**:
- Email: `user@bookstore.com`
- Password: `password`

> ⚠️ **Important**: Change these default passwords in production!

---

## User Interface (Customer Side)

### 1. Homepage Features
- **Featured Books**: Browse latest and popular books
- **Quick Statistics**: View total books, users, and categories
- **Navigation**: Easy access to login, register, and browse functions

### 2. User Registration
1. Click "Register" from the homepage or login page
2. Fill in required information:
   - Full Name
   - Email Address
   - Password
   - Confirm Password
3. Submit to create your account
4. Login with your new credentials

### 3. User Dashboard (`/src/user/`)

#### Navigation Bar Features:
- **Home**: User dashboard overview
- **All Books**: Browse complete book catalog
- **Categories**: Filter books by category
- **History**: View purchase history
- **Profile**: Manage account settings
- **Logout**: Exit the system

#### Book Browsing:
- **View All Books**: Complete catalog with search functionality
- **Category Filter**: Browse books by specific categories
- **Book Details**: Click any book for detailed information
- **Search**: Find books by title or author

#### Book Purchase Process:
1. Navigate to book details page
2. Click "Buy Now" button
3. Review purchase details
4. Confirm transaction
5. Transaction recorded in your history

### 4. User Profile Management
- **View Profile**: See your account information
- **Update Profile**: Edit name and contact details
- **Purchase History**: Track all your book purchases
- **Transaction Details**: View individual purchase information

---

## Admin Panel (Management Side)

### 1. Admin Dashboard (`/src/admin/`)

#### Navigation Menu:
- **Dashboard**: Overview with statistics and charts
- **Books Management**: Add, edit, delete books
- **Categories**: Manage book categories
- **Users**: View and manage user accounts
- **Transactions**: Monitor and manage orders
- **Reviews**: Manage customer reviews
- **Activity Logs**: Track admin activities

### 2. Books Management

#### Add New Book:
1. Click "Books" → "Add Book"
2. Fill in book information:
   - Title
   - Author
   - Category
   - Description
   - Price (in Rupiah)
   - Stock quantity
   - Cover image URL
3. Save to add to catalog

#### Edit Existing Book:
1. Go to Books list
2. Click "Edit" on desired book
3. Modify information
4. Save changes

#### Delete Book:
1. From Books list, click "Delete"
2. Confirm deletion
3. Book removed from catalog

### 3. Category Management

#### Add Category:
1. Navigate to "Categories"
2. Click "Add New Category"
3. Enter category name and description
4. Save

#### Manage Categories:
- Edit existing categories
- Delete unused categories
- View books per category

### 4. User Management

#### View Users:
- See all registered users
- Filter by role (admin/user)
- View registration dates

#### User Actions:
- View user details
- Check user purchase history
- Monitor user activities

### 5. Transaction Management

#### Monitor Transactions:
- View all customer purchases
- Filter by status (pending/completed)
- See transaction details

#### Update Transaction Status:
1. Find transaction in list
2. Click "Edit" or "Update Status"
3. Change from "pending" to "completed"
4. Save changes

#### Transaction Details Include:
- Customer information
- Books purchased
- Quantities and prices
- Transaction date
- Payment status

### 6. Review Management
- View all customer reviews
- Monitor ratings and comments
- Moderate inappropriate content
- Respond to customer feedback

### 7. Activity Logs
- Track all admin actions
- Monitor system changes
- View timestamps and details
- Export logs for analysis

---

## Key Features Explained

### 1. Search and Filter System
**User Side**:
- Search books by title or author
- Filter by categories
- Sort by price or date added

**Admin Side**:
- Filter transactions by status
- Search users by name or email
- Sort data by various criteria

### 2. Shopping Cart System
- Add books to purchase
- Review before checkout
- Calculate total prices
- Process transactions

### 3. Review System
- Rate books (1-5 stars)
- Write detailed reviews
- View community feedback
- Help other customers decide

### 4. Activity Tracking
**User Activities**:
- Login/logout events
- Purchase history
- Profile updates

**Admin Activities**:
- Book additions/modifications
- User management actions
- Transaction updates

---

## Best Practices

### For Users:
1. **Secure Your Account**:
   - Use strong passwords
   - Logout when finished
   - Keep profile information updated

2. **Smart Shopping**:
   - Read reviews before purchasing
   - Check book descriptions carefully
   - Monitor your purchase history

3. **Community Participation**:
   - Leave honest reviews
   - Rate books you've purchased
   - Help other readers

### For Administrators:
1. **Regular Maintenance**:
   - Update book information regularly
   - Monitor transaction status
   - Review user feedback

2. **Content Management**:
   - Keep book catalog current
   - Maintain accurate stock levels
   - Update categories as needed

3. **User Support**:
   - Process transactions promptly
   - Respond to user issues
   - Monitor review content

---

## Advanced Features

### 1. Reporting and Analytics
- View sales statistics
- Monitor popular books
- Track user engagement
- Export data for analysis

### 2. Bulk Operations
- Import multiple books
- Batch update prices
- Mass category changes
- Bulk user management

### 3. System Maintenance
- Database backup procedures
- Regular security updates
- Performance monitoring
- Error log review

---

## Troubleshooting Common Issues

### User Issues:

**Can't Login**:
- Check email and password spelling
- Ensure account is registered
- Clear browser cache/cookies

**Purchase Not Working**:
- Verify sufficient stock
- Check transaction status
- Contact administrator

**Profile Updates Failing**:
- Use valid email format
- Ensure all required fields filled
- Check for duplicate emails

### Admin Issues:

**Book Images Not Displaying**:
- Verify image URL is valid
- Check image format (JPG, PNG)
- Ensure URL is accessible

**Transaction Status Not Updating**:
- Refresh the page
- Check database connection
- Verify user permissions

**Activity Logs Missing**:
- Check system timestamps
- Verify logging functions
- Review database status

---

## Data Management

### Backup Procedures:
1. **Database Backup**:
   - Export from phpMyAdmin
   - Use MySQL dump commands
   - Schedule regular backups

2. **File Backup**:
   - Copy entire project folder
   - Include configuration files
   - Store in secure location

### Data Import/Export:
- Export transaction reports
- Import book catalogs
- Transfer user data
- Migrate between systems

---

## Security Guidelines

### For All Users:
- Use HTTPS in production
- Keep software updated
- Regular password changes
- Monitor access logs

### Admin Responsibilities:
- Review user accounts regularly
- Monitor suspicious activities
- Maintain access controls
- Update security patches

---

## Support and Maintenance

### Regular Tasks:
1. **Daily**:
   - Check for new transactions
   - Monitor system performance
   - Review user feedback

2. **Weekly**:
   - Update book inventory
   - Process pending transactions
   - Review activity logs

3. **Monthly**:
   - Database maintenance
   - Security updates
   - Performance optimization
   - Backup verification

### Getting Help:
- Check error logs first
- Review documentation
- Contact system administrator
- Submit support tickets

---

## API Integration (Future Enhancement)

The system is designed to support:
- Payment gateway integration
- Email notification system
- Third-party book databases
- Mobile app connectivity

---

This usage guide covers the main functionality of the Belva Bookstore system. For installation instructions, see [INSTALLATION.md](INSTALLATION.md). For technical details, refer to the source code documentation.
