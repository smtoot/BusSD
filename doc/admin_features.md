# TransLab Admin Feature Analysis

## 1. Dashboard & Overview
The administrative dashboard provides a high-level summary of the system's health and performance.
- **Key Metrics**: Total Owners, Active Owners, Email/Mobile Unverified Owners.
- **Financial Stats**: Total Deposited, Pending Deposits, Total Transactions.
- **Visual Reports**: Interactive charts for deposit statistics and transaction reports.

## 2. Owner (User) Management
Admin has full control over the "Owners" (the customers using the transport system).
- **Segmentation**: List users by status: All, Active, Banned, Email Verified/Unverified, Mobile Verified/Unverified.
- **Detail View**: Full profile summary, active subscriptions, total deposits, and transaction history.
- **Actions**:
  - **Edit Profile**: Update name, contact info, and address.
  - **Account Status**: Ban or unban with a mandatory reason record.
  - **Impersonation**: "Login as Owner" feature for debugging or direct assistance.
  - **Communication**: Send targeted Email, SMS, or Push notifications to individuals or groups.

## 3. Subscription & Service Packages
The core business model revolves around selling access packages to transport owners.
- **Package Management**: Create plans with custom:
  - Pricing (Monthly/Yearly/Custom).
  - Time Limits (Duration of the subscription).
  - Unit Limits (Possible limits on buses/counters/trips).
- **Features Catalog**: List specific features included in each plan to be displayed on landing pages.

## 4. Financial & Payment Gateway Management
- **Automatic Gateways**: Pre-integrated support for Stripe, Razorpay, Mollie, PayPal, BTC Pay, etc.
- **Manual Gateways**: Ability to create custom payment methods (Bank Transfer, Mobile Money) with custom instructions and input fields.
- **Payment Lifecycle**:
  - Monitoring all transaction stages: Pending, Approved, Successful, Rejected.
  - **Manual Approval**: Admin reviews manual payment proof (attachments) and approves/rejects with feedback.

## 5. Content Management System (CMS) & SEO
- **Frontend Section Manager**: Directly edit content for landing page sections (e.g., Hero, About, Features, FAQ, Testimonials).
- **Template System**: Switch between different frontend themes (if available).
- **Page Builder**: Create and manage custom pages (Terms, Privacy, Custom Landing Pages).
- **Global SEO**: Manage site-wide meta tags, social media images, and keywords.
- **Robots & Sitemap**: Automated and manual controls for search engine indexing.

## 6. Support & Feedback
- **Ticket System**: Complete helpdesk for Owners.
- **Communication**: Multi-threaded replies, file attachment support, and status management (Pending, Answered, Closed).
- **Bug Reports**: Dedicated portal for users to report technical issues, complete with attachment support for screenshots.

## 7. System Settings & Internationalization
- **General Settings**: Branding (Title, Site Color, Logo, Icon), Currency configuration, and Timezone.
- **System Configuration**: Fine-grained control over system-wide toggles (Registry, Login, Email Verification, SMS Verification, etc.).
- **Multi-Language Support**:
  - Add/Remove languages.
  - In-browser JSON editor for translating all system labels and messages.
- **Notification Settings**:
  - Global templates for Email/SMS/Push strings.
  - Driver configuration (SMTP, SendGrid, Twilio, Firebase).

## 8. Maintenance & Tools
- **System Information**: Detailed server environment overview.
- **One-Click Update**: Integrated mechanism to pull and apply software updates.
- **Performance Tools**: UI buttons for `php artisan optimize` and `cache:clear`.
- **Maintenance Mode**: Toggle to temporarily disable the site for users with a custom message.
- **Extension Manager**: Enable/Disable 3rd party plugins like Google Analytics, Tawk.to live chat, and Facebook Comments.
