@extends('layout')

@section('content')
<style>
    .password-calculator {
        max-width: 800px;
        margin: 60px auto;
        padding: 40px 20px;
    }

    .calculator-card {
        background: white;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .page-title {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 10px;
        color: #1a1a1a;
    }

    .page-subtitle {
        text-align: center;
        color: #666;
        margin-bottom: 40px;
        font-size: 1.1rem;
    }

    .password-input-wrapper {
        position: relative;
        margin-bottom: 30px;
    }

    .password-input {
        width: 100%;
        padding: 16px 50px 16px 16px;
        font-size: 18px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-family: 'Courier New', monospace;
    }

    .password-input:focus {
        outline: none;
        border-color: #2196F3;
        box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
    }

    .toggle-visibility {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        font-size: 20px;
        color: #666;
        padding: 4px;
    }

    .toggle-visibility:hover {
        color: #2196F3;
    }

    /* Password Strength Bar */
    .strength-bar-container {
        margin-bottom: 30px;
    }

    .strength-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .strength-text {
        font-weight: 600;
    }

    .strength-bar {
        display: flex;
        gap: 6px;
        height: 8px;
        margin-bottom: 12px;
    }

    .strength-segment {
        flex: 1;
        border-radius: 4px;
        background-color: #e0e0e0;
        transition: all 0.3s ease;
    }

    .strength-segment.active.very-weak { background-color: #f44336; }
    .strength-segment.active.weak { background-color: #ff9800; }
    .strength-segment.active.medium { background-color: #ffc107; }
    .strength-segment.active.strong { background-color: #8bc34a; }
    .strength-segment.active.very-strong { background-color: #4caf50; }

    /* Requirements Checklist */
    .requirements {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .requirements-title {
        font-weight: 600;
        margin-bottom: 12px;
        color: #333;
    }

    .requirement-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        font-size: 14px;
        color: #666;
    }

    .requirement-icon {
        margin-right: 8px;
        font-size: 16px;
    }

    .requirement-icon.met { color: #4caf50; }
    .requirement-icon.unmet { color: #e0e0e0; }

    /* Statistics Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 24px;
        border-radius: 12px;
        text-align: center;
    }

    .stat-card.combinations {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stat-card.crack-time {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .stat-label {
        font-size: 13px;
        opacity: 0.9;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        line-height: 1.2;
    }

    .stat-subtext {
        font-size: 12px;
        opacity: 0.8;
        margin-top: 4px;
    }

    /* Brute Force Info */
    .brute-force-info {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .brute-force-info h3 {
        margin-top: 0;
        margin-bottom: 12px;
        color: #856404;
        font-size: 18px;
    }

    .brute-force-info p {
        margin: 8px 0;
        color: #856404;
        font-size: 14px;
        line-height: 1.6;
    }

    .gpu-specs {
        background: white;
        padding: 12px;
        border-radius: 6px;
        margin-top: 12px;
        font-size: 13px;
        color: #666;
    }

    /* Tips Section */
    .tips-section {
        background: #e3f2fd;
        border-left: 4px solid #2196F3;
        padding: 20px;
        border-radius: 8px;
    }

    .tips-section h3 {
        margin-top: 0;
        margin-bottom: 12px;
        color: #1565c0;
        font-size: 18px;
    }

    .tips-section ul {
        margin: 0;
        padding-left: 20px;
    }

    .tips-section li {
        margin-bottom: 8px;
        color: #1565c0;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .calculator-card {
            padding: 24px;
        }

        .page-title {
            font-size: 2rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="password-calculator">
    <div class="calculator-card">
        <h1 class="page-title">🔐 Password Strength Calculator</h1>
        <p class="page-subtitle">Test your password strength and see how long it would take to crack with brute-force attacks</p>

        <!-- Password Input -->
        <div class="password-input-wrapper">
            <input 
                type="password" 
                id="passwordInput" 
                class="password-input" 
                placeholder="Enter your password to test..."
                autocomplete="off"
                autocapitalize="off"
                autocorrect="off"
                spellcheck="false"
            >
            <button class="toggle-visibility" id="toggleBtn" type="button">
                👁️
            </button>
        </div>

        <!-- Strength Bar -->
        <div class="strength-bar-container">
            <div class="strength-label">
                <span>Password Strength:</span>
                <span class="strength-text" id="strengthText">Enter a password</span>
            </div>
            <div class="strength-bar">
                <div class="strength-segment" data-segment="0"></div>
                <div class="strength-segment" data-segment="1"></div>
                <div class="strength-segment" data-segment="2"></div>
                <div class="strength-segment" data-segment="3"></div>
                <div class="strength-segment" data-segment="4"></div>
            </div>
        </div>

        <!-- Requirements Checklist -->
        <div class="requirements">
            <div class="requirements-title">Password Requirements:</div>
            <div class="requirement-item">
                <span class="requirement-icon unmet" id="req-length">○</span>
                <span>At least 8 characters</span>
            </div>
            <div class="requirement-item">
                <span class="requirement-icon unmet" id="req-upper">○</span>
                <span>Uppercase letters (A-Z)</span>
            </div>
            <div class="requirement-item">
                <span class="requirement-icon unmet" id="req-lower">○</span>
                <span>Lowercase letters (a-z)</span>
            </div>
            <div class="requirement-item">
                <span class="requirement-icon unmet" id="req-number">○</span>
                <span>Numbers (0-9)</span>
            </div>
            <div class="requirement-item">
                <span class="requirement-icon unmet" id="req-special">○</span>
                <span>Special characters (!@#$%^&*)</span>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card combinations">
                <div class="stat-label">Total Combinations</div>
                <div class="stat-value" id="combinations">-</div>
                <div class="stat-subtext" id="combinations-scientific">-</div>
            </div>
            <div class="stat-card crack-time">
                <div class="stat-label">Time to Crack</div>
                <div class="stat-value" id="crackTime">-</div>
                <div class="stat-subtext">With powerful GPU</div>
            </div>
        </div>

        <!-- Brute Force Info -->
        <div class="brute-force-info">
            <h3>⚡ Brute Force Attack Simulation</h3>
            <p><strong>Attack Method:</strong> <span id="attackMethod">-</span></p>
            <p><strong>Character Pool Size:</strong> <span id="charPoolSize">-</span> characters</p>
            <div class="gpu-specs">
                <strong>GPU Specs:</strong> NVIDIA RTX 4090 (~100 billion hashes/second for SHA-256)
                <br>
                <strong>Note:</strong> This is a theoretical estimate. Real-world cracking times vary based on hash algorithm, hardware, and attack sophistication.
            </div>
        </div>

        <!-- Tips Section -->
        <div class="tips-section">
            <h3>💡 Tips for Creating Strong Passwords</h3>
            <ul>
                <li>Use at least 12-16 characters for maximum security</li>
                <li>Mix uppercase, lowercase, numbers, and special characters</li>
                <li>Avoid common words, names, and predictable patterns</li>
                <li>Don't reuse passwords across different accounts</li>
                <li>Consider using a passphrase (e.g., "Coffee!Morning@2024")</li>
                <li>Use a password manager to generate and store complex passwords</li>
                <li>Enable two-factor authentication (2FA) whenever possible</li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('passwordInput');
    const toggleBtn = document.getElementById('toggleBtn');
    const strengthText = document.getElementById('strengthText');
    const combinationsEl = document.getElementById('combinations');
    const combinationsScientificEl = document.getElementById('combinations-scientific');
    const crackTimeEl = document.getElementById('crackTime');
    const attackMethodEl = document.getElementById('attackMethod');
    const charPoolSizeEl = document.getElementById('charPoolSize');

    // Toggle password visibility
    toggleBtn.addEventListener('click', function() {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        toggleBtn.textContent = type === 'password' ? '👁️' : '🙈';
    });

    // Password analysis
    passwordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        analyzePassword(password);
    });

    function analyzePassword(password) {
        if (!password) {
            resetDisplay();
            return;
        }

        // Check requirements
        const requirements = {
            length: password.length >= 8,
            upper: /[A-Z]/.test(password),
            lower: /[a-z]/.test(password),
            number: /\d/.test(password),
            special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
        };

        updateRequirements(requirements);

        // Calculate character pool size
        let charPoolSize = 0;
        if (requirements.lower) charPoolSize += 26;
        if (requirements.upper) charPoolSize += 26;
        if (requirements.number) charPoolSize += 10;
        if (requirements.special) charPoolSize += 32;

        // Calculate total combinations
        const combinations = Math.pow(charPoolSize, password.length);
        
        // GPU hashing speed (SHA-256): ~100 billion hashes/second for RTX 4090
        const hashesPerSecond = 100_000_000_000;
        const secondsToCrack = combinations / hashesPerSecond;

        // Calculate strength score (0-4)
        let score = calculateStrength(password, requirements);

        // Update UI
        updateStrengthBar(score);
        updateStatistics(combinations, secondsToCrack, charPoolSize);
    }

    function calculateStrength(password, requirements) {
        let score = 0;
        const length = password.length;

        // Length contribution (40%)
        if (length >= 16) score += 2;
        else if (length >= 12) score += 1.5;
        else if (length >= 8) score += 1;
        else if (length >= 4) score += 0.5;

        // Variety contribution (60%)
        let varietyScore = 0;
        if (requirements.upper) varietyScore += 0.5;
        if (requirements.lower) varietyScore += 0.5;
        if (requirements.number) varietyScore += 0.5;
        if (requirements.special) varietyScore += 0.5;

        // Bonus for all character types
        if (requirements.upper && requirements.lower && requirements.number && requirements.special) {
            varietyScore += 0.5;
        }

        score += varietyScore * 1.5;

        return Math.min(Math.floor(score), 4);
    }

    function updateRequirements(requirements) {
        updateRequirement('req-length', requirements.length);
        updateRequirement('req-upper', requirements.upper);
        updateRequirement('req-lower', requirements.lower);
        updateRequirement('req-number', requirements.number);
        updateRequirement('req-special', requirements.special);
    }

    function updateRequirement(id, met) {
        const el = document.getElementById(id);
        if (met) {
            el.textContent = '✓';
            el.classList.remove('unmet');
            el.classList.add('met');
        } else {
            el.textContent = '○';
            el.classList.remove('met');
            el.classList.add('unmet');
        }
    }

    function updateStrengthBar(score) {
        const strengthLevels = ['Very Weak', 'Weak', 'Medium', 'Strong', 'Very Strong'];
        const strengthClasses = ['very-weak', 'weak', 'medium', 'strong', 'very-strong'];

        strengthText.textContent = strengthLevels[score];
        strengthText.style.color = getStrengthColor(score);

        // Update segments
        document.querySelectorAll('.strength-segment').forEach((segment, index) => {
            segment.classList.remove('active', 'very-weak', 'weak', 'medium', 'strong', 'very-strong');
            if (index <= score) {
                segment.classList.add('active', strengthClasses[score]);
            }
        });
    }

    function getStrengthColor(score) {
        const colors = ['#f44336', '#ff9800', '#ffc107', '#8bc34a', '#4caf50'];
        return colors[score];
    }

    function updateStatistics(combinations, seconds, charPoolSize) {
        // Format combinations
        if (combinations === Infinity || isNaN(combinations)) {
            combinationsEl.textContent = 'Infinite';
            combinationsScientificEl.textContent = '';
        } else if (combinations > 1e100) {
            combinationsEl.textContent = '∞';
            combinationsScientificEl.textContent = 'Virtually unlimited';
        } else {
            combinationsEl.textContent = formatNumber(combinations);
            combinationsScientificEl.textContent = formatScientific(combinations);
        }

        // Format crack time
        crackTimeEl.textContent = formatTime(seconds);

        // Update attack method
        charPoolSizeEl.textContent = charPoolSize;
        attackMethodEl.textContent = `Brute force (${charPoolSize}-character pool)`;
    }

    function formatNumber(num) {
        if (num < 1000) return num.toString();
        if (num < 1000000) return (num / 1000).toFixed(1) + 'K';
        if (num < 1000000000) return (num / 1000000).toFixed(1) + 'M';
        if (num < 1000000000000) return (num / 1000000000).toFixed(1) + 'B';
        if (num < 1000000000000000) return (num / 1000000000000).toFixed(1) + 'T';
        return (num / 1000000000000000).toFixed(1) + 'Q';
    }

    function formatScientific(num) {
        if (num < 1e6) return '';
        return num.toExponential(2);
    }

    function formatTime(seconds) {
        if (seconds === Infinity || isNaN(seconds)) return 'Instant';
        if (seconds < 0.001) return 'Instant';
        if (seconds < 1) return (seconds * 1000).toFixed(2) + ' milliseconds';
        if (seconds < 60) return seconds.toFixed(2) + ' seconds';
        if (seconds < 3600) return (seconds / 60).toFixed(2) + ' minutes';
        if (seconds < 86400) return (seconds / 3600).toFixed(2) + ' hours';
        if (seconds < 604800) return (seconds / 86400).toFixed(2) + ' days';
        if (seconds < 2628000) return (seconds / 604800).toFixed(2) + ' weeks';
        if (seconds < 31536000) return (seconds / 2628000).toFixed(2) + ' months';
        if (seconds < 3153600000) return (seconds / 31536000).toFixed(2) + ' years';
        if (seconds < 31536000000) return (seconds / 31536000).toFixed(0) + ' years';
        
        const centuries = seconds / 3153600000;
        if (centuries < 1000) return centuries.toFixed(0) + ' centuries';
        if (centuries < 1000000) return (centuries / 1000).toFixed(0) + 'K centuries';
        if (centuries < 1000000000) return (centuries / 1000000).toFixed(0) + 'M centuries';
        
        return 'Heat death of universe';
    }

    function resetDisplay() {
        strengthText.textContent = 'Enter a password';
        strengthText.style.color = '#666';
        combinationsEl.textContent = '-';
        combinationsScientificEl.textContent = '-';
        crackTimeEl.textContent = '-';
        attackMethodEl.textContent = '-';
        charPoolSizeEl.textContent = '-';

        document.querySelectorAll('.strength-segment').forEach(segment => {
            segment.classList.remove('active', 'very-weak', 'weak', 'medium', 'strong', 'very-strong');
        });

        ['req-length', 'req-upper', 'req-lower', 'req-number', 'req-special'].forEach(id => {
            const el = document.getElementById(id);
            el.textContent = '○';
            el.classList.remove('met');
            el.classList.add('unmet');
        });
    }
});
</script>
@endsection