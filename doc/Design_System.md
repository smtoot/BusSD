# BusConnect Partner Portal - Complete Design System Documentation

## Project Overview

**Project Name:** BusConnect Partner Portal  
**Industry:** Bus Transportation Management (Saudi Arabia)  
**Target Users:** Bus company owners, operations managers, booking agents, accountants  
**Platform Type:** Comprehensive SaaS Web Application  
**Purpose:** Manage fleet, trips, bookings, revenue, and operations  
**Design Approach:** Modern, clean, data-centric design with full bilingual support (English LTR & Arabic RTL)

---

## 1. BRAND IDENTITY

### Brand Colors (CSS Variables)

```css
/* Primary Brand Colors */
--busconnect-primary: #28A745;        /* Vibrant Green - Primary actions, CTAs, success states */
--busconnect-secondary: #004085;      /* Deep Blue - Secondary actions, accents */

/* Neutral Colors */
--busconnect-dark-grey: #343A40;      /* Dark text, headings */
--busconnect-medium-grey: #6C757D;    /* Secondary text, labels */
--busconnect-light-grey: #F8F9FA;     /* Backgrounds, dividers */

/* Semantic Colors */
--busconnect-success: #28A745;        /* Success messages, positive indicators */
--busconnect-warning: #FFC107;        /* Warnings, caution states */
--busconnect-danger: #DC3545;         /* Errors, destructive actions */
--busconnect-info: #17A2B8;           /* Informational messages */
```

### Design System Color Tokens

```css
:root {
  /* Background & Foreground */
  --background: #ffffff;
  --foreground: #343A40;
  
  /* Card Components */
  --card: #ffffff;
  --card-foreground: #343A40;
  
  /* Popover Components */
  --popover: #ffffff;
  --popover-foreground: #343A40;
  
  /* Primary Theme */
  --primary: #28A745;
  --primary-foreground: #ffffff;
  
  /* Secondary Theme */
  --secondary: #004085;
  --secondary-foreground: #ffffff;
  
  /* Muted Elements */
  --muted: #F8F9FA;
  --muted-foreground: #6C757D;
  
  /* Accent Elements */
  --accent: #F8F9FA;
  --accent-foreground: #343A40;
  
  /* Destructive Actions */
  --destructive: #DC3545;
  --destructive-foreground: #ffffff;
  
  /* Borders & Inputs */
  --border: rgba(108, 117, 125, 0.2);
  --input: transparent;
  --input-background: #F8F9FA;
  --switch-background: #6C757D;
  
  /* Focus & Selection */
  --ring: #28A745;
  
  /* Border Radius */
  --radius: 0.5rem;
}
```

### Chart Colors

```css
--chart-1: #28A745;  /* Primary Green */
--chart-2: #004085;  /* Deep Blue */
--chart-3: #17A2B8;  /* Info Blue */
--chart-4: #FFC107;  /* Warning Yellow */
--chart-5: #DC3545;  /* Danger Red */
```

---

## 2. TYPOGRAPHY

### Font Families

**English (LTR):**
- Primary: `'Poppins', sans-serif`
- Weights: 400 (Regular), 500 (Medium), 600 (Semi-Bold), 700 (Bold)

**Arabic (RTL):**
- Primary: `'Cairo', sans-serif`
- Weights: 400 (Regular), 500 (Medium), 600 (Semi-Bold), 700 (Bold)

### Font Implementation

```css
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap');

body {
  font-family: 'Poppins', sans-serif;
}

[dir="rtl"] {
  font-family: 'Cairo', sans-serif;
}
```

### Typography Scale

```css
:root {
  --font-size: 16px;
  --font-weight-medium: 500;
  --font-weight-normal: 400;
}

/* Base Typography Hierarchy */
h1 {
  font-size: var(--text-2xl);     /* ~24px */
  font-weight: var(--font-weight-medium);
  line-height: 1.5;
}

h2 {
  font-size: var(--text-xl);      /* ~20px */
  font-weight: var(--font-weight-medium);
  line-height: 1.5;
}

h3 {
  font-size: var(--text-lg);      /* ~18px */
  font-weight: var(--font-weight-medium);
  line-height: 1.5;
}

h4 {
  font-size: var(--text-base);    /* 16px */
  font-weight: var(--font-weight-medium);
  line-height: 1.5;
}

p {
  font-size: var(--text-base);    /* 16px */
  font-weight: var(--font-weight-normal);
  line-height: 1.5;
}

label {
  font-size: var(--text-base);
  font-weight: var(--font-weight-medium);
  line-height: 1.5;
}

button {
  font-size: var(--text-base);
  font-weight: var(--font-weight-medium);
  line-height: 1.5;
}

input {
  font-size: var(--text-base);
  font-weight: var(--font-weight-normal);
  line-height: 1.5;
}
```

---

## 3. SPACING & LAYOUT

### Spacing System (Tailwind-based)

- `0.5rem` = 8px (gap-2, p-2, m-2)
- `0.75rem` = 12px (gap-3, p-3, m-3)
- `1rem` = 16px (gap-4, p-4, m-4)
- `1.5rem` = 24px (gap-6, p-6, m-6)
- `2rem` = 32px (gap-8, p-8, m-8)
- `3rem` = 48px (gap-12, p-12, m-12)

### Border Radius System

```css
--radius-sm: calc(var(--radius) - 4px);   /* 0.125rem = 2px */
--radius-md: calc(var(--radius) - 2px);   /* 0.25rem = 4px */
--radius-lg: var(--radius);                /* 0.5rem = 8px */
--radius-xl: calc(var(--radius) + 4px);   /* 0.75rem = 12px */
```

### Component-Specific Spacing

**Card Components:**
- Header Padding: `px-6 pt-6`
- Content Padding: `px-6`
- Footer Padding: `px-6 pb-6`
- Internal Gap: `gap-6`

**Table Components:**
- Cell Padding: `p-2`
- Head Height: `h-10`
- Consistent horizontal padding: `px-2`

---

## 4. COMPONENTS

### 4.1 Buttons

**Variants:**
- `default`: Primary green background (`bg-primary`)
- `destructive`: Red background for dangerous actions (`bg-destructive`)
- `outline`: Bordered with transparent background
- `secondary`: Deep blue background (`bg-secondary`)
- `ghost`: Transparent with hover effect
- `link`: Text link style with underline

**Sizes:**
- `default`: `h-9 px-4 py-2`
- `sm`: `h-8 px-3` (small)
- `lg`: `h-10 px-6` (large)
- `icon`: `size-9` (square icon buttons)

**Button Implementation:**
```tsx
<Button variant="default" size="default">Primary Action</Button>
<Button variant="secondary">Secondary Action</Button>
<Button variant="outline">Outlined Button</Button>
<Button variant="destructive">Delete</Button>
<Button variant="ghost">Subtle Action</Button>
```

### 4.2 Cards

**Structure:**
```tsx
<Card>
  <CardHeader>
    <CardTitle>Card Title</CardTitle>
    <CardDescription>Card Description</CardDescription>
    <CardAction>Action Button</CardAction>
  </CardHeader>
  <CardContent>
    Main content goes here
  </CardContent>
  <CardFooter>
    Footer actions
  </CardFooter>
</Card>
```

**Styling:**
- Border: `border` (subtle gray)
- Border Radius: `rounded-xl`
- Background: `bg-card`
- Default gap: `gap-6`

### 4.3 Badges

**Variants:**
- `default`: Green background (primary)
- `secondary`: Blue background
- `destructive`: Red background
- `outline`: Bordered with transparent background

**Custom Status Badges:**
```css
.status-upcoming {
  background-color: #E3F2FD;
  color: #1565C0;
}

.status-completed {
  background-color: #E8F5E8;
  color: #2E7D32;
}

.status-canceled {
  background-color: #FFEBEE;
  color: #C62828;
}
```

**Implementation:**
```tsx
<Badge variant="default">Active</Badge>
<Badge variant="secondary">Pending</Badge>
<Badge variant="destructive">Cancelled</Badge>
<Badge className="status-upcoming">Upcoming</Badge>
```

### 4.4 Input Fields

**Base Input Styling:**
- Height: `h-9`
- Border Radius: `rounded-md`
- Background: `bg-input-background` (#F8F9FA)
- Border: `border-input`
- Padding: `px-3 py-1`
- Focus Ring: 3px ring with primary color

**States:**
- Default: Light gray background
- Focus: Primary green ring (`focus-visible:ring-ring`)
- Invalid: Destructive red ring (`aria-invalid:border-destructive`)
- Disabled: Reduced opacity (0.5)

### 4.5 Tables

**Structure:**
```tsx
<Table>
  <TableHeader>
    <TableRow>
      <TableHead>Column 1</TableHead>
      <TableHead>Column 2</TableHead>
    </TableRow>
  </TableHeader>
  <TableBody>
    <TableRow>
      <TableCell>Data 1</TableCell>
      <TableCell>Data 2</TableCell>
    </TableRow>
  </TableBody>
</Table>
```

**Styling:**
- Cell Padding: `p-2`
- Row Height: Automatic with padding
- Hover State: `hover:bg-muted/50`
- Border: Bottom border on rows
- Text Size: `text-sm`

**Enhanced Spacing (Arabic RTL fix applied):**
- Increased horizontal padding in Trip Management tables
- Better icon-text relationships
- Proper alignment for both LTR and RTL

### 4.6 Alerts

**Variants:**
- `default`: Standard card background
- `destructive`: Red text with red icon

**Structure:**
```tsx
<Alert variant="default">
  <AlertIcon />
  <AlertTitle>Alert Title</AlertTitle>
  <AlertDescription>Alert description text</AlertDescription>
</Alert>
```

### 4.7 Sidebar Navigation

**Sidebar Colors:**
```css
--sidebar: #ffffff;
--sidebar-foreground: #343A40;
--sidebar-primary: #28A745;
--sidebar-primary-foreground: #ffffff;
--sidebar-accent: #F8F9FA;
--sidebar-accent-foreground: #343A40;
--sidebar-border: rgba(108, 117, 125, 0.2);
--sidebar-ring: #28A745;
```

**Active State:**
- Left border (LTR): 3px solid primary green
- Right border (RTL): 3px solid primary green
- Background: Subtle accent background
- Text: Primary color

**Hover State:**
- Background gradient from accent color
- Smooth transition

---

## 5. RTL (RIGHT-TO-LEFT) SUPPORT

### Core RTL Implementation

```css
/* Set RTL direction */
[dir="rtl"] {
  font-family: 'Cairo', sans-serif;
}

/* RTL Text Alignment */
[dir="rtl"] .rtl\:text-right {
  text-align: right;
}

[dir="rtl"] .rtl\:ml-auto {
  margin-left: auto;
}

[dir="rtl"] .rtl\:mr-0 {
  margin-right: 0;
}
```

### RTL Sidebar Positioning

```css
/* Sidebar Border - Right side for RTL */
[dir="rtl"] [data-sidebar="sidebar"] {
  border-left: 1px solid hsl(var(--sidebar-border));
  border-right: none;
}

/* Sidebar Positioning */
[dir="rtl"] [data-side="right"] {
  right: 0;
  left: auto;
}

[dir="rtl"] [data-side="right"][data-collapsible="offcanvas"] {
  right: calc(var(--sidebar-width) * -1);
  left: auto;
}

/* Flex Direction Reversal */
[dir="rtl"] .sidebar-wrapper-rtl {
  flex-direction: row-reverse;
}
```

### RTL Menu Items

```css
/* Menu Button RTL Alignment */
[dir="rtl"] [data-sidebar="menu-button"] {
  text-align: right;
  flex-direction: row-reverse;
}

/* Icon Spacing */
[dir="rtl"] [data-sidebar="menu-button"] > span {
  margin-right: 0.5rem;
  margin-left: 0;
}

/* Custom RTL Item Class */
[dir="rtl"] .sidebar-item-rtl {
  direction: rtl;
  text-align: right;
  justify-content: flex-end;
}

[dir="rtl"] .sidebar-item-rtl .sidebar-icon {
  margin-left: 0.5rem;
  margin-right: 0;
}
```

### RTL Active States

```css
/* Active Border on Right Side */
[dir="rtl"] [data-sidebar="menu-button"][data-active="true"] {
  border-right: 3px solid hsl(var(--sidebar-primary));
  border-left: none;
  padding-right: calc(0.5rem - 3px);
}

/* Active Background Positioning */
[dir="rtl"] [data-sidebar="menu-button"][data-active="true"]::before {
  right: 0;
  left: auto;
}
```

### RTL Hover Effects

```css
[dir="rtl"] [data-sidebar="menu-button"]:hover {
  background: linear-gradient(90deg, transparent, hsl(var(--sidebar-accent)));
}
```

### RTL Content Alignment

```css
[dir="rtl"] [data-sidebar="content"] {
  text-align: right;
}

[dir="rtl"] [data-sidebar="header"] {
  text-align: right;
}

[dir="rtl"] [data-sidebar="footer"] {
  text-align: right;
}
```

### RTL Dropdown Support

```css
[dir="rtl"] [data-radix-popper-content-wrapper] {
  direction: rtl;
}

[dir="rtl"] .dropdown-item-rtl {
  flex-direction: row-reverse;
  text-align: right;
}

[dir="rtl"] .dropdown-item-rtl .dropdown-icon {
  margin-left: 0.5rem;
  margin-right: 0;
}
```

### React Implementation for RTL

```tsx
// Language State
const [language, setLanguage] = useState<'en' | 'ar'>('en');

// Toggle Language Function
const toggleLanguage = () => {
  setLanguage(prev => prev === 'en' ? 'ar' : 'en');
};

// Set RTL direction effect
useEffect(() => {
  document.documentElement.dir = language === 'ar' ? 'rtl' : 'ltr';
  document.documentElement.lang = language;
}, [language]);
```

---

## 6. CUSTOM FEATURES

### 6.1 Seat Selection Component

**Seat States:**
```css
.seat {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  border: 2px solid #E0E0E0;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 600;
  font-size: 14px;
}

/* Available Seat */
.seat-available {
  background-color: #F8F9FA;
  border-color: #E0E0E0;
  color: #6C757D;
}

.seat-available:hover {
  background-color: #E8F5E8;
  border-color: #28A745;
  color: #28A745;
  box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
}

/* BusConnect Booking */
.seat-busconnect {
  background-color: #28A745;
  border-color: #28A745;
  color: white;
  box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.seat-busconnect:hover {
  background-color: #218838;
  border-color: #218838;
  box-shadow: 0 6px 16px rgba(40, 167, 69, 0.4);
}

/* Manual Booking */
.seat-manual {
  background-color: #004085;
  border-color: #004085;
  color: white;
}

/* Hover Effect */
.seat:hover:not(:disabled) {
  transform: scale(1.1);
}

/* Disabled State */
.seat:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}
```

**Seat Selection Animation:**
```css
@keyframes seatSelect {
  0% { transform: scale(1); }
  50% { transform: scale(1.2); }
  100% { transform: scale(1.1); }
}

.seat-busconnect {
  animation: seatSelect 0.3s ease-out;
}
```

### 6.2 KPI Cards (Dashboard)

**Structure:**
- Icon in colored circle background
- Large metric value (h2 or h3)
- Descriptive label below
- Optional trend indicator or secondary metric

**Common Icon Styling:**
- Size: `h-12 w-12` or `h-10 w-10`
- Padding: `p-3` or `p-2.5`
- Border Radius: `rounded-full` or `rounded-lg`
- Background: Primary or accent color
- Icon Color: White or contrasting color

### 6.3 Notification/Alert Cards

**Notification Types:**
- Warning: AlertTriangle icon, yellow accent
- Success: CheckCircle icon, green accent
- Error: XCircle icon, red accent
- Info: AlertCircle icon, blue accent

**Structure:**
```tsx
<Alert className="border-l-4 border-l-warning">
  <AlertTriangle className="h-4 w-4 text-warning" />
  <AlertDescription>Notification message</AlertDescription>
  <Button variant="outline" size="sm">Action</Button>
</Alert>
```

---

## 7. ICON SYSTEM

### Icon Library
**Package:** `lucide-react`

**Common Icons Used:**
- **Navigation:** LayoutDashboard, Route, Bookmark, Truck, Users, Settings
- **Actions:** Plus, Edit, Trash2, Eye, Download, Upload, RefreshCw
- **Status:** CheckCircle, XCircle, AlertTriangle, AlertCircle, Clock
- **Finance:** DollarSign, CreditCard
- **Users:** User, UserCheck, UserPlus
- **Transport:** Bus, Truck, MapPin
- **Communication:** MessageSquare, Bell, Mail
- **Misc:** Calendar, Star, HelpCircle, Globe, Shield, ArrowRight

**Icon Sizing:**
- Small: `h-4 w-4` (16px)
- Medium: `h-5 w-5` (20px)
- Large: `h-6 w-6` (24px)
- Extra Large: `h-8 w-8` or `h-12 w-12` (for hero sections)

---

## 8. CHARTS & DATA VISUALIZATION

### Chart Library
**Package:** `recharts`

**Chart Types Used:**
- Line Charts: Revenue trends, performance metrics
- Bar Charts: Comparisons, categorical data
- Area Charts: Cumulative data over time

**Chart Color Scheme:**
```tsx
<Line stroke="var(--chart-1)" />  {/* Primary Green */}
<Line stroke="var(--chart-2)" />  {/* Deep Blue */}
<Line stroke="var(--chart-3)" />  {/* Info Blue */}
<Line stroke="var(--chart-4)" />  {/* Warning Yellow */}
<Line stroke="var(--chart-5)" />  {/* Danger Red */}
```

**Chart Container:**
```tsx
<ResponsiveContainer width="100%" height={300}>
  <LineChart data={data}>
    <CartesianGrid strokeDasharray="3 3" stroke="#E0E0E0" />
    <XAxis dataKey="name" stroke="#6C757D" />
    <YAxis stroke="#6C757D" />
    <Tooltip />
    <Line type="monotone" dataKey="value" stroke="var(--chart-1)" strokeWidth={2} />
  </LineChart>
</ResponsiveContainer>
```

---

## 9. RESPONSIVE DESIGN

### Breakpoints (Tailwind)
- `sm`: 640px and up
- `md`: 768px and up
- `lg`: 1024px and up
- `xl`: 1280px and up
- `2xl`: 1536px and up

### Mobile-First Approach
- Default styles target mobile
- Use `md:`, `lg:`, `xl:` prefixes for larger screens
- Cards stack vertically on mobile, grid on desktop
- Sidebar collapses on mobile, fixed on desktop

### Responsive Patterns
```tsx
{/* Grid - 1 col mobile, 2 cols tablet, 3 cols desktop */}
<div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

{/* Flex - Stack on mobile, row on desktop */}
<div className="flex flex-col md:flex-row gap-4">

{/* Hide on mobile, show on desktop */}
<div className="hidden lg:block">

{/* Full width on mobile, fixed width on desktop */}
<div className="w-full lg:w-96">
```

---

## 10. STATES & INTERACTIONS

### Focus States
- Ring: 3px ring with primary color
- Border: Primary color border
- Class: `focus-visible:ring-ring focus-visible:ring-[3px]`

### Hover States
- Buttons: Slight background darkening (90% opacity)
- Cards: Subtle background color shift
- Links: Underline appears
- Table Rows: `hover:bg-muted/50`

### Active States
- Sidebar Items: 3px colored border (left for LTR, right for RTL)
- Tabs: Underline or background color
- Toggle Switches: Checked state uses primary color

### Disabled States
- Opacity: 0.5
- Cursor: `cursor-not-allowed`
- Pointer Events: None
- Class: `disabled:opacity-50 disabled:pointer-events-none`

### Loading States
- Skeleton components for loading content
- Spinner icons for actions in progress
- Progress bars for multi-step processes

---

## 11. ACCESSIBILITY

### ARIA Labels
- Always provide `aria-label` for icon-only buttons
- Use `role="alert"` for alert components
- Use `aria-invalid` for form validation

### Keyboard Navigation
- All interactive elements are keyboard accessible
- Focus states are clearly visible
- Logical tab order maintained

### Color Contrast
- All text meets WCAG AA standards (4.5:1 minimum)
- Interactive elements have sufficient contrast
- Error states use both color and icons

### Screen Reader Support
- Semantic HTML structure (h1, h2, nav, main, etc.)
- Proper form labels
- Meaningful link text

---

## 12. TRANSLATION SYSTEM

### Translation Structure
```tsx
const translations = {
  en: {
    key: 'English Text',
    // ... more keys
  },
  ar: {
    key: 'النص العربي',
    // ... more keys
  }
};

// Usage
const t = translations[language];
<h1>{t.key}</h1>
```

### Bilingual Content Strategy
- All user-facing text has English and Arabic translations
- Numbers and dates formatted according to locale
- Currency always displayed as "SAR" (Saudi Riyal)
- Icons remain universal (no language-specific icons)

### Common Translation Keys
- Navigation: dashboard, trips, bookings, fleet, drivers, financials, reviews, complaints, help, settings
- Actions: add, edit, delete, view, save, cancel, submit, search, filter
- Status: active, inactive, pending, completed, cancelled, upcoming
- Time: today, week, month, year, last30days

---

## 13. ANIMATION & TRANSITIONS

### Transition Properties
```css
transition: all 0.3s ease;
transition-all
```

### Common Animations
- Button hover: Scale or background change
- Seat selection: Scale up (1.1x) with shadow
- Sidebar menu hover: Gradient background
- Card hover: Subtle lift with shadow
- Form focus: Ring appearance

### Animation Classes
```css
/* Smooth transitions */
transition-colors
transition-all
transition-opacity

/* Transform */
hover:scale-105
hover:-translate-y-1
```

---

## 14. FORM PATTERNS

### Form Field Structure
```tsx
<div className="space-y-2">
  <Label htmlFor="field-id">Field Label</Label>
  <Input id="field-id" placeholder="Placeholder text" />
</div>
```

### Form Layout
- Vertical stacking with `space-y-4` or `space-y-6`
- Two-column layout on desktop: `grid grid-cols-1 md:grid-cols-2 gap-6`
- Section headers to group related fields
- Clear visual hierarchy

### Validation States
- Error: Red border and ring (`aria-invalid`)
- Success: Green checkmark icon
- Warning: Yellow indicator
- Helper text below input in muted color

---

## 15. DATA DISPLAY PATTERNS

### Metrics Display
```tsx
<div className="flex items-center gap-2">
  <div className="bg-primary/10 p-2.5 rounded-lg">
    <Icon className="h-5 w-5 text-primary" />
  </div>
  <div>
    <div className="text-2xl font-medium">1,234</div>
    <div className="text-sm text-muted-foreground">Metric Label</div>
  </div>
</div>
```

### Status Indicators
- Badge components with semantic colors
- Icon + text combinations
- Color-coded backgrounds with accessible contrast

### Empty States
- Centered layout
- Illustrative icon
- Helpful message
- Call-to-action button

---

## 16. NAVIGATION PATTERNS

### Main Navigation (Sidebar)
- Fixed left (LTR) or right (RTL) sidebar
- Logo at top
- Menu items with icons and labels
- Active state indicator (border)
- Collapse functionality on mobile
- User profile at bottom

### Secondary Navigation
- Tabs for content sections
- Breadcrumbs for hierarchy
- Back buttons for detail views

### Action Navigation
- "Add New" buttons (primary action)
- Filter/search bars (secondary actions)
- Bulk action toolbars (conditional)

---

## 17. FEEDBACK PATTERNS

### Success Messages
- Green checkmark icon
- Success badge or alert
- Toast notifications (sonner library)
- Inline confirmation messages

### Error Messages
- Red X icon or AlertTriangle
- Destructive badge or alert
- Field-level validation messages
- Toast notifications for system errors

### Loading Indicators
- Spinner icons for button actions
- Skeleton screens for page loads
- Progress bars for multi-step processes
- Loading overlays for data fetching

---

## 18. CONTENT HIERARCHY

### Visual Hierarchy Principles
1. **Primary Content:** Largest, darkest, most prominent
2. **Secondary Content:** Medium size, medium weight
3. **Tertiary Content:** Small, muted color, supporting info

### Spacing Hierarchy
- Large gaps (gap-8, gap-12) between major sections
- Medium gaps (gap-6) between related groups
- Small gaps (gap-4) within groups
- Minimal gaps (gap-2) for tightly related items

### Color Hierarchy
- Primary actions: Green (primary brand color)
- Secondary actions: Blue or outlined
- Destructive actions: Red
- Neutral actions: Gray or ghost variant

---

## 19. MODAL & OVERLAY PATTERNS

### Dialog/Modal Structure
```tsx
<Dialog>
  <DialogTrigger>Open Modal</DialogTrigger>
  <DialogContent>
    <DialogHeader>
      <DialogTitle>Modal Title</DialogTitle>
      <DialogDescription>Modal description</DialogDescription>
    </DialogHeader>
    {/* Content */}
    <DialogFooter>
      <Button variant="outline">Cancel</Button>
      <Button>Confirm</Button>
    </DialogFooter>
  </DialogContent>
</Dialog>
```

### Sheet (Drawer) Pattern
- Slide-in from right (LTR) or left (RTL)
- Used for filters, forms, details
- Full-height overlay
- Close button in header

---

## 20. TECHNICAL IMPLEMENTATION NOTES

### CSS Framework
- **Tailwind CSS v4** (latest version)
- Utility-first approach
- Custom design tokens in `globals.css`
- JIT compilation for optimized builds

### Component Library Foundation
- Built on **Radix UI** primitives
- Customized with **class-variance-authority (CVA)**
- Styled with Tailwind utility classes
- Accessible by default

### React Best Practices
- Functional components with hooks
- TypeScript for type safety
- Props interfaces for all components
- Reusable components in `/components` directory

### File Structure
```
/components
  /ui (shadcn-style component library)
    - button.tsx
    - card.tsx
    - input.tsx
    - table.tsx
    - badge.tsx
    - alert.tsx
    - dialog.tsx
    - etc.
  - Layout.tsx (main layout with sidebar)
  - Dashboard.tsx
  - [Feature].tsx (feature-specific components)
/styles
  - globals.css (design tokens and custom styles)
App.tsx (main app with routing logic)
```

### State Management
- React useState for local state
- Props drilling for simple apps
- Context API for global state (user, language)
- Navigation state managed in App.tsx

---

## 21. COMPLETE IMPLEMENTATION CHECKLIST

### When Starting a New Project Using This Design System:

**✅ Step 1: Setup CSS & Fonts**
1. Copy entire `globals.css` file
2. Ensure Google Fonts imports for Poppins and Cairo
3. Set up CSS variables for all color tokens
4. Include RTL support styles

**✅ Step 2: Create UI Component Library**
1. Set up `/components/ui` folder
2. Implement core components: Button, Card, Input, Badge, Table, Alert, Dialog
3. Use CVA for variant management
4. Ensure all components support RTL

**✅ Step 3: Language & RTL Support**
1. Set up language state (`en` | `ar`)
2. Create translation objects for all text
3. Implement `useEffect` to set `dir` and `lang` attributes
4. Test all layouts in both LTR and RTL

**✅ Step 4: Layout & Navigation**
1. Create `Layout.tsx` with sidebar
2. Implement responsive sidebar (collapsible on mobile)
3. Add user profile section
4. Support RTL sidebar positioning

**✅ Step 5: Feature Components**
1. Build feature-specific components
2. Use consistent spacing (gap-6 for sections, gap-4 for groups)
3. Implement loading states and error handling
4. Add accessibility attributes

**✅ Step 6: Testing**
1. Test all components in both English and Arabic
2. Verify RTL alignment and spacing
3. Check color contrast for accessibility
4. Test responsive behavior on mobile, tablet, desktop

---

## 22. BRAND VOICE & TONE

### English (EN)
- Professional yet approachable
- Clear and concise
- Action-oriented language
- Positive and empowering

### Arabic (AR)
- Formal business Arabic
- Respectful and professional
- Clear instructions
- Culturally appropriate

---

## 23. SPECIAL CONSIDERATIONS FOR SAUDI MARKET

### Cultural Adaptations
- Friday-Saturday weekend (not Saturday-Sunday)
- Right-to-left reading direction natural for primary audience
- Gender-neutral language and icons
- Compliance with Saudi transportation regulations

### Currency & Measurements
- Currency: SAR (Saudi Riyal)
- Distance: Kilometers (km)
- Time: 24-hour format or 12-hour with AM/PM
- Dates: Support both Gregorian and Hijri calendars

### Legal & Compliance
- Driver license expiration tracking
- Vehicle registration compliance
- Insurance documentation
- Regulatory reporting features

---

## 24. PERFORMANCE GUIDELINES

### Optimization Strategies
- Lazy load feature components
- Use React.memo for heavy components
- Optimize images (use WebP where possible)
- Minimize bundle size with tree shaking
- Use CSS-in-JS sparingly (prefer Tailwind)

### Loading Strategy
- Skeleton screens for initial load
- Progressive enhancement
- Optimistic UI updates
- Error boundaries for graceful failures

---

## SUMMARY

This comprehensive design system for **BusConnect Partner Portal** provides:

- **Complete visual language** (colors, typography, spacing)
- **Bilingual support** (English LTR & Arabic RTL) with native RTL experience
- **Component library** (buttons, cards, forms, tables, etc.)
- **Accessibility standards** (WCAG AA compliant)
- **Responsive design** (mobile-first approach)
- **Cultural appropriateness** (Saudi market considerations)
- **Technical implementation** (React, TypeScript, Tailwind CSS v4)

**To replicate this design system in another project:**
1. Copy the `globals.css` file in its entirety
2. Import required fonts (Poppins, Cairo)
3. Build UI component library using the specifications above
4. Implement RTL support CSS rules
5. Create translation system for bilingual content
6. Follow component patterns and spacing conventions
7. Test thoroughly in both languages and all viewports

This design system ensures consistency, accessibility, and a premium user experience for bus transportation companies across Saudi Arabia.

---

**Design System Version:** 1.0  
**Last Updated:** February 11, 2026  
**Maintained By:** BusConnect Development Team  
**License:** Proprietary - For BusConnect Projects Only
