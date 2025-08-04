# Buyunic Technologies - Enrollment Portal

A comprehensive web-based enrollment portal for Buyunic Technologies ICT training programs. This modern, responsive portal allows students to browse courses, register for programs, and make payments online.

## 🌟 Features

### Home Page (`index.html`)
- **Modern Landing Page**: Eye-catching hero section with gradient backgrounds
- **Course Catalog**: Interactive filterable course cards organized by categories
- **Course Categories**:
  - Microsoft Office (Word, PowerPoint, Excel, Access)
  - Graphics & Web Design (Photoshop, HTML, WordPress)
  - Other Programs (Networking, SPSS, CCTV)
  - Internship Programs
- **About Section**: Company mission, values, and training excellence
- **Payment Information**: Mobile Money and Pesapal integration details
- **Responsive Design**: Mobile-first approach with Tailwind CSS

### Registration System (`auth/register.html`)
- **Comprehensive Registration Form**:
  - Personal Information (Name, Email, Phone, DOB, Gender, Address)
  - Education Background (Level, Institution, Graduation Year, Field of Study)
  - Course Selection (Dynamic course loading based on category)
  - Emergency Contact Information
  - Additional Information (Motivation, Experience, Referral source)
- **Dynamic Course Selection**: Courses populate based on selected category
- **Application ID Generation**: Unique ID generated for payment processing
- **Form Validation**: Client-side validation with required fields
- **Success Confirmation**: Clear success message with payment link

### Payment System (`payment.html`)
- **Payment Methods**:
  - Mobile Money (MTN, Airtel)
  - Pesapal (Credit/Debit Cards, Bank Transfer)
- **Application ID Integration**: Links registration to payment
- **Payment Form**: Secure form with validation
- **Payment Instructions**: Step-by-step guidance
- **Support Information**: Multiple contact channels

## 🎨 Design System

### Color Palette
- **Primary**: `#1e40af` (Blue)
- **Secondary**: `#059669` (Green)
- **Accent Colors**: Various shades for course categories

### Typography
- **Primary Font**: Inter (Google Fonts)
- **Accent Font**: Pacifico (for branding)

### UI Components
- **Custom Border Radius**: 8px button radius
- **Icons**: Remix Icons library
- **Animations**: Hover effects, transitions, and transforms
- **Cards**: Gradient backgrounds with shadows

## 🛠️ Technology Stack

- **HTML5**: Semantic markup
- **CSS3**: Custom styling with CSS variables
- **Tailwind CSS**: Utility-first CSS framework
- **JavaScript**: Vanilla JS for interactivity
- **Remix Icons**: Icon library
- **Google Fonts**: Typography

## 📱 Responsive Design

The portal is fully responsive with breakpoints for:
- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: > 1024px

## 🚀 Getting Started

### Prerequisites
- Modern web browser
- Local web server (optional, for development)

### Installation
1. Clone or download the repository
2. Open `index.html` in a web browser
3. For development, use a local server:
   ```bash
   # Using Python
   python -m http.server 8000
   
   # Using Node.js
   npx serve .
   ```

### File Structure
```
/
├── index.html              # Home page
├── payment.html            # Payment processing page
├── logo.svg               # Company logo (SVG)
├── logo.png               # Company logo (PNG)
├── README.md              # Documentation
└── auth/
    └── register.html      # Registration form
```

## 💳 Course Pricing (UGX)

### Microsoft Office
- **MS Word Training**: 120,000 UGX (3 weeks)
- **PowerPoint Mastery**: 50,000 UGX (2 weeks)
- **Excel Advanced**: 60,000 UGX (2 weeks)
- **Access Database**: 60,000 UGX (2 weeks)

### Graphics & Web Design
- **Photoshop & Design**: 550,000 UGX (4 weeks)
- **HTML Development**: 600,000 UGX (4 weeks)
- **WordPress Mastery**: 500,000 UGX (4 weeks)

### Other Programs
- **Networking & SPSS**: 550,000 UGX (6 weeks)
- **CCTV Systems**: 550,000 UGX (3 weeks)

### Internship
- **Internship Program**: 400,000 UGX (2 months)

## 🔧 Customization

### Adding New Courses
1. Update the course data in `auth/register.html` (JavaScript section)
2. Add corresponding course cards in `index.html`
3. Update pricing information as needed

### Styling Changes
- Modify Tailwind configuration in the `<script>` tag
- Update CSS custom properties for colors
- Adjust responsive breakpoints as needed

### Payment Integration
- Replace demo payment forms with actual payment gateway APIs
- Update payment processing logic in `payment.html`
- Add server-side payment verification

## 📞 Contact Information

- **Phone**: +256 394 839 851
- **WhatsApp**: +256 207 901 434
- **Email**: info@buyunic.com
- **Address**: Plot 28, North-Road, Mbale City

## 🌐 Social Media

- **Facebook**: [buyunicug](https://www.facebook.com/buyunicug/)
- **Twitter**: [@buyunict](https://x.com/buyunict)
- **YouTube**: [@buyunic](https://www.youtube.com/@buyunic)
- **Instagram**: [@buyunic](https://instagram.com/buyunic)

## 📄 Legal

- **Privacy Policy**: [buyunic.ug/privacy-policy](https://buyunic.ug/privacy-policy/)
- **Terms & Conditions**: Available on request
- **Refund Policy**: Non-refundable once training begins

## 🔮 Future Enhancements

- [ ] Backend integration with database
- [ ] Real payment gateway integration
- [ ] Student dashboard
- [ ] Course progress tracking
- [ ] Email notifications
- [ ] Admin panel for course management
- [ ] Multi-language support
- [ ] PWA capabilities

## 📝 License

© 2025 Buyunic Technologies. All rights reserved.

---

**Built with ❤️ for empowering ICT skills in Uganda**