# OIE-Main

## 📘 Project Description

**OIE-Main** is a comprehensive **OJT/Internship Program Management System** designed to support and streamline the internship processes required as part of a college curriculum. It serves as a centralized platform for students, advisers, coordinators, and company representatives to effectively coordinate all aspects of the On-the-Job Training (OJT) program.

### 🔑 Key Features

- **Job Postings Portal**  
  Allows companies to post internship opportunities while students can browse and apply for roles aligned with their qualifications and interests.

- **Cloud-Based File Management**  
  Enables students to upload OJT documents (e.g., resumes, logs, reports). Coordinators and advisers can remotely review and manage submissions.

- **OJT Clearance and Compliance Monitoring**  
  Tracks hours rendered, document status, and clearance to ensure timely student compliance.

- **Integrated Grading System**  
  Facilitates student evaluations by both advisers and companies, automatically computing final grades based on configurable rubrics.

- **Academic Date System**  
  Manages internship-related dates such as semester timelines, document submission deadlines, and evaluation windows.

The system enhances **efficiency, transparency, and collaboration**, reducing manual paperwork and administrative burden.

---

# OJT Management System – Full Guide

## 🚀 System Initialization & Setup

1. **System Initialization**
   - CIPA Director registers to activate the system.
   - Manages colleges, departments, and courses.

2. **Academic Calendar Setup**
   - Defines OJT semester start and end dates.
   - Used as the time reference across all features.

3. **Student Enrollment**
   - Coordinators enroll students either manually or via Excel import.
   - Automatically creates student folders in Google Drive.

4. **Document Preparation**
   - Coordinators upload downloadable templates.
   - Option to allow multiple document submissions per student.

5. **Adviser Assignment**
   - Coordinators assign advisers to sections and departments.

## 👤 Student Access & Deployment

6. **Student Registration**
   - Students sign up using student number and institutional email.
   - Email OTP verification required.
   - Inputs personal info and uploads resume.

7. **Deployment Process**
   - Students apply to partnered companies via portal.
   - If accepted, deployment auto-updates and awaits adviser approval.
   - For outside companies, CIPA creates accounts post-MOA.

8. **Change Company Requests**
   - Students can request to switch companies.
   - Adviser approval required for redeployment.

9. **Industry Partner Registration**
   - CIPA creates company accounts from the portal.

## 📄 Document Management

10. **OJT Document Submission & Approval**
   - Students submit completed documents.
   - Industry partners review and approve via their portal.
   - Supports bulk (multi-) document approvals.

## 📊 Grading System

11. **Grading Criteria & Final Grade Calculation**
   - Coordinators define criteria and weights per company or in bulk.
   - Advisers and companies provide grades via portal or email forms.
   - System auto-computes final grade based on rubric settings.

12. **New Semester Setup**
   - Repeat calendar and enrollment steps to begin a new OJT cycle.

## ⏱ Time & Activity Tracking

13. **DTR Monitoring**
   - Students time-in/out and record daily work hours.
   - Company trainers approve logs for validity.
   - System marks OJT complete once required hours are reached.

14. **Announcements**
   - CIPA and Coordinators post reminders/events viewable by students.

15. **Trainer Assignments**
   - Company reps assign trainers via Intern Tracker view.

## 📈 Dashboard & Analytics

16. **Coordinator Dashboard**
   - Displays real-time stats: total enrolled, deployed, undeployed, and deployment trends.

17. **Job Posts Management**
   - Companies manage job listings, target colleges, and track student assignments.

18. **MOA/MOU Tracking**
   - CIPA monitors uploaded MOAs, assigns validity duration based on upload date.

---

## 👥 User Roles & Access

| Role            | Responsibilities                                                                 |
|-----------------|----------------------------------------------------------------------------------|
| **CIPA Director** | Admin operations, account creation, MOA tracking, calendar & announcement setup |
| **Coordinator**   | Manages enrollment, documents, grading rubrics, and evaluation criteria         |
| **Adviser**       | Approves deployments, grades students, verifies documents                      |
| **Student**       | Applies for internships, uploads files, logs time, views grades                |
| **Industry Partner** | Reviews/approves documents, assigns trainers, evaluates students             |

---

## ⚙️ Technology Stack

- **Frontend**: HTML, CSS, JavaScript, Bootstrap  
- **Backend**: PHP, MySQL  
- **Third-Party Integrations**: Google Drive API, OTP Email Verification

---

## 📄 License

This project is intended for academic use only unless explicitly authorized by the development team.
