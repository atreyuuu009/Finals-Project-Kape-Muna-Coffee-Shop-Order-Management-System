<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Staff Account</title>
    <link rel="stylesheet" href="create-styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <span class="icon">👥</span>
                Create Staff Account
            </h1>
            <p>Add new team member to the system</p>
        </div>

        <div class="form-content">
            <div class="alert">
                <div class="alert-title">🔒 Admin Access Required</div>
                <div class="alert-text">
                    Only Manager/Admin accounts can create new staff members. All credentials will be sent to the staff member's email.
                </div>
            </div>

            <form id="staffForm" method="POST" action="auth_register.php" novalidate>
            
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name <span class="required">*</span></label>
                        <input type="text" id="firstName" name="firstName" placeholder="Juan" required>
                        <div class="error-message" id="firstNameError"></div>
                    </div>
                    <div class="form-group">
                        <label>Last Name <span class="required">*</span></label>
                        <input type="text" id="lastName" name="lastName" placeholder="Dela Cruz" required>
                        <div class="error-message" id="lastNameError"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="juan.delacruz@example.com" required>
                    <div class="error-message" id="emailError"></div>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="+63 912 345 6789">
                </div>

                <div class="form-group">
                    <label>Username <span class="required">*</span></label>
                    <input type="text" id="username" name="username" placeholder="jdelacruz" required>
                    <div class="help-text">Username must be unique and at least 4 characters long</div>
                    <div class="error-message" id="usernameError"></div>
                </div>
                <input type="hidden" id="role" name="role" value="">


                <div class="form-row">
                    <div class="form-group">
                        <label>Password <span class="required">*</span></label>
                        <input type="password" id="password" name="password" required>
                        <div class="error-message" id="passwordError"></div>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password <span class="required">*</span></label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required>
                        <div class="error-message" id="confirmPasswordError"></div>
                    </div>
                </div>

                <div class="help-text" style="margin-top: -10px; margin-bottom: 20px;">
                    Password must be at least 8 characters with letters and numbers
                </div>

                <div class="role-section">
                    <label>Assign Role <span class="required">*</span></label>
                    <div class="role-cards">
                        <div class="role-card" data-role="staff">
                            <div class="role-icon">👤</div>
                            <div class="role-name">Staff</div>
                            <div class="role-description">Basic access, view orders</div>
                        </div>
                        <div class="role-card" data-role="cashier">
                            <div class="role-icon">💵</div>
                            <div class="role-name">Cashier</div>
                            <div class="role-description">Process orders & payments</div>
                        </div>
                        <div class="role-card" data-role="manager">
                            <div class="role-icon">👔</div>
                            <div class="role-name">Manager</div>
                            <div class="role-description">Full system access</div>
                        </div>
                    </div>
                    <div class="error-message" id="roleError"></div>
                </div>

                <div class="button-group">
                    <button type="button" class="btn btn-cancel" id="cancelBtn">Cancel</button>
                    
                    <button type="submit" class="btn btn-submit">Create Account</button>
                </div>
            </form>
        </div>
    </div>

    <div class="success-modal" id="successModal">
        <div class="success-content">
            <div class="success-icon">✅</div>
            <h2>Account Created Successfully!</h2>
            <p>Staff account has been created. Please login with your credentials.</p>
            <p style="font-size: 13px; color: #999; margin-top: 10px;">Redirecting to login page in 1 second...</p>
            <button class="btn btn-submit" id="closeModal">Go to Login Now</button>
        </div>
    </div>

    <script src="create-script.js"></script>
    
</body>
</html>