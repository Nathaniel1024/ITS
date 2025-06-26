# ITS

1. Create the database and its tables using the `all.sql` file located in the `ITS/src/sql` folder.

2. Register a user by visiting: [http://localhost/ITS/src/register.php](http://localhost/ITS/src/register.php)

3. After creating a user. You will be redirected to the login page which is in [http://localhost/ITS/src/index.php](http://localhost/ITS/src/index.php)

# ITS Pages Overview

---

## 1. Dashboard (`dashboard.php`)

- **Purpose:**  
  The main landing page after login, providing an overview of treasury operations.
- **Features:**  
  - Displays summary charts and statistics (e.g., collections, disbursements, balances).
  - Allows searching and filtering of tracking details by Tracking ID, Department, or Status.
  - Provides quick access to recent tracking records and paginated tables.
  - Sidebar navigation for easy access to other modules.

---

## 2. Transaction Tracking (`transaction_track.php`)

- **Purpose:**  
  Manage and monitor the tracking of financial transactions.
- **Features:**  
  - List of transactions that require tracking details.
  - Ability to add, view, edit, or delete tracking details for each transaction.
  - Modal forms for managing tracking information (department, payment method, status, remarks).
  - Displays activity history for each tracking record.
  - Sidebar navigation for module switching.

---

## 3. Transactions (`transaction.php`)

- **Purpose:**  
  Manage all financial transactions (collections and disbursements).
- **Features:**  
  - Add new transactions with payer name, type, amount, guarantor, and status.
  - Edit or delete existing transactions.
  - View transaction history for each record.
  - Summary cards for total collections, disbursements, and remaining balance.
  - Table of recent transactions with status badges and action buttons.

---

## 4. Online Billing (`online_billing.php`)

- **Purpose:**  
  Manage and view online billing records.
- **Features:**  
  - (Implementation details may vary; typically includes listing, searching, and managing online billing entries.)
  - May provide download or print options for billing statements.

---

## 5. Download Page (`download_page.php`)

- **Purpose:**  
  Central location for downloading reports, receipts, or other documents.
- **Features:**  
  - List of available files for download (e.g., receipts, reports).
  - Secure download links.
  - May include filters or search for specific documents.

---

## 6. Logout (`logout.php`)

- **Purpose:**  
  Securely log out the current user.
- **Features:**  
  - Ends the user session and redirects to the login page.
  - Ensures all session data is cleared for security.

---