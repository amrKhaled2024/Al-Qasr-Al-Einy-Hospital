# Kasr Al Ainy Hospital Management System

A complete full-stack hospital management system implementing OOP principles and design patterns.

## Features

- **Three User Roles**: Admin, Receptionist, Doctor, Patient
- **Design Patterns**: Singleton, Factory, Observer, Strategy, Decorator
- **MVC Architecture**: Clean separation of concerns
- **Responsive UI**: Modern hospital-themed interface
- **Secure Authentication**: Role-based access control
- **Real-time Notifications**: Observer pattern for appointment updates

## Installation

1. Clone the repository
2. Import database schema: `hospital_management.sql`
3. Configure database in `config/database.php`
4. Run `composer install`
5. Access via web server

## Default Credentials

- Admin: admin@hospital.com / admin123
- Doctor: doctor@hospital.com / doctor123
- Receptionist: reception@hospital.com / reception123

## Design Patterns Used

1. **Singleton**: HospitalSystem class
2. **Factory**: UserFactory for creating different user types
3. **Observer**: Appointment notifications to patients
4. **Strategy**: Authentication strategies
5. **Decorator**: User permission extensions

## UML Diagrams

See `docs/` directory for:
- Class Diagram
- Sequence Diagrams
- Use Case Diagrams
- ER Diagram

## Testing

Run unit tests:
```bash
php tests/UnitTests.php