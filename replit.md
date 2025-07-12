# DropShip Pro - Development Guide

## Overview

DropShip Pro is a modern e-commerce dropshipping platform built with a focus on performance, user experience, and scalability. The application uses a hybrid approach combining server-side rendering with dynamic client-side interactions through HTMX for a seamless user experience.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture
- **Technology Stack**: HTML5, CSS3 (Custom CSS with Bootstrap integration), JavaScript (ES6+)
- **UI Framework**: Bootstrap-based custom design system with CSS custom properties for theming
- **Interactive Framework**: HTMX for dynamic content updates without full page reloads
- **Design Approach**: Component-based styling with custom CSS variables for consistent theming

### Backend Architecture
- **Server Technology**: Not explicitly defined in current codebase (likely Node.js/Express or similar)
- **API Strategy**: HTMX-driven endpoints for dynamic content delivery
- **Architecture Pattern**: Server-side rendering with progressive enhancement

### Styling System
- **CSS Architecture**: Custom properties (CSS variables) for theme management
- **Component System**: Bootstrap-enhanced custom components with hover effects and transitions
- **Color Palette**: Comprehensive color system with semantic naming (primary, secondary, success, danger, etc.)
- **Responsive Design**: Bootstrap grid system with custom enhancements

## Key Components

### Theme System
- **CSS Custom Properties**: Centralized color and spacing variables in `:root`
- **Interactive Elements**: Enhanced buttons and cards with hover animations
- **Typography**: Professional font stack with Segoe UI as primary

### JavaScript Application Core
- **App Object**: Centralized configuration and initialization system
- **Event Management**: Global event listener setup for cart updates, search, and user interactions
- **Component Initialization**: Bootstrap component integration (tooltips, popovers, carousels, modals)
- **HTMX Integration**: Custom event handlers for loading states and dynamic content

### User Interface Features
- **Shopping Cart**: Real-time updates with custom events
- **Search**: Debounced search functionality with configurable delay
- **Form Validation**: Client-side validation system
- **Image Optimization**: Lazy loading implementation
- **Accessibility**: Keyboard navigation support

## Data Flow

### Client-Side Interactions
1. **User Actions**: Trigger HTMX requests or JavaScript event handlers
2. **State Management**: Local state updates through custom events (e.g., 'cartUpdated')
3. **UI Updates**: Dynamic content updates via HTMX or direct DOM manipulation
4. **Feedback**: Loading states and animations provide user feedback

### Component Communication
- **Event-Driven**: Custom events for component communication
- **Configuration**: Centralized app configuration object
- **Progressive Enhancement**: Base functionality works without JavaScript

## External Dependencies

### Frontend Libraries
- **Bootstrap**: UI framework for responsive design and components
- **HTMX**: Dynamic content loading and form handling
- **Browser APIs**: Intersection Observer (for lazy loading), localStorage (implied for cart)

### Development Tools
- **CSS**: Custom properties for theming, CSS transitions for animations
- **JavaScript**: ES6+ features, event-driven architecture

## Deployment Strategy

### Asset Organization
- **Static Assets**: Organized in `/assets` directory with separate CSS and JS folders
- **Modular CSS**: Single stylesheet with organized sections (global styles, overrides, components)
- **JavaScript Architecture**: Single main application file with modular organization

### Performance Considerations
- **Lazy Loading**: Implemented for images to improve initial page load
- **Debounced Search**: Prevents excessive API calls during user input
- **CSS Transitions**: Hardware-accelerated animations for smooth interactions
- **Progressive Enhancement**: Core functionality works without JavaScript

### Browser Compatibility
- **Modern CSS**: Custom properties and modern CSS features
- **JavaScript**: ES6+ syntax suggests modern browser targeting
- **Fallback Strategy**: Progressive enhancement approach ensures basic functionality

## Development Guidelines

### Code Organization
- **Separation of Concerns**: Clear separation between structure (HTML), presentation (CSS), and behavior (JavaScript)
- **Component-Based**: Reusable CSS components with consistent naming
- **Event-Driven**: JavaScript architecture based on custom events and listeners

### Styling Conventions
- **CSS Variables**: Use custom properties for consistent theming
- **BEM-like Structure**: Implied component-based CSS organization
- **Bootstrap Integration**: Custom overrides that enhance rather than replace Bootstrap

### JavaScript Patterns
- **Namespace Pattern**: App object contains all application logic
- **Event Delegation**: Global event listeners with proper binding
- **Configuration Management**: Centralized configuration object for easy maintenance