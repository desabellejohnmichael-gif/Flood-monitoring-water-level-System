-- Sample admin account
-- Username: admin
-- Password: admin123
-- Email: admin@waterlevel.com

INSERT INTO users (username, password, email, role, created_at) 
VALUES (
    'admin', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- This is the hashed version of 'admin123'
    'admin@waterlevel.com',
    'admin',
    CURRENT_TIMESTAMP
) ON DUPLICATE KEY UPDATE id=id;

-- Insert a sample admin key
INSERT INTO admin_keys (key_hash, created_at)
VALUES (
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- This is the same hash, you can use 'admin123' as the admin key
    CURRENT_TIMESTAMP
);