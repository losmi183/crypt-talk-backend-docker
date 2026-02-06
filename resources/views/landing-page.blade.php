@extends('layout')

@section('content')
    <!-- Hero Section -->
    <section class="hero">
        <h1>Your Privacy, Our Priority</h1>
        <p>Experience truly secure messaging with military-grade end-to-end encryption. Your encryption keys never touch the internet - making it mathematically impossible to decrypt your messages. Not even we can read them.</p>
        <div class="hero-buttons">
            <a href="https://web.crypt-talk.online/" class="btn btn-primary">Launch Web App</a>
            <a href="#platforms" class="btn btn-secondary">Download Apps</a>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <h2 class="section-title">You Control Your Security Level</h2>
            <p class="section-subtitle">The strength of your password determines the level of protection for your messages. Test your password strength and see how long it would take to crack with a brute-force attack using our <a href="/password-calculator" class="inline-link">🔐 Password Strength Calculator</a></p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>End-to-End Encryption</h3>
                    <p>Every message is encrypted directly on your device using AES-256 encryption. Only you and your recipient possess the keys to decrypt and read your conversations.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🔑</div>
                    <h3>Client-Side Encryption</h3>
                    <p>Encryption keys are generated and stored exclusively on your device. Share keys offline with trusted contacts for maximum security - no key ever touches our servers.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3>PBKDF2 Key Derivation</h3>
                    <p>Your password goes through 100,000 rounds of cryptographic hashing before generating encryption keys. This process slows down brute-force attacks by 100,000x - turning a 6-day attack into 1,600+ years. Even weak passwords become exponentially harder to crack, giving you time to change them if compromised.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🚫</div>
                    <h3>Zero Knowledge Architecture</h3>
                    <p>We mathematically cannot read your messages. Our servers only store encrypted data - even system administrators and law enforcement cannot access your conversations.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Real-Time Messaging</h3>
                    <p>Enjoy instant, lightning-fast communication without compromising security. Our optimized encryption ensures zero lag while maintaining maximum protection.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🌐</div>
                    <h3>Cross-Platform Sync</h3>
                    <p>Seamlessly access your encrypted conversations across web, mobile, and desktop. Your messages sync instantly while remaining completely secure.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <h2 class="section-title">How Our Encryption Works</h2>
            <p class="section-subtitle">Understanding the cutting-edge technology that keeps your messages private, secure, and completely unreadable to anyone except you.</p>
            
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Create Your Encryption Key</h3>
                    <p>Choose a strong password that serves as your encryption key. Using symmetric cryptography (AES-256), the same password encrypts and decrypts messages on both ends - keeping your key completely offline.</p>
                </div>

                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Share Keys Through Secure Channels</h3>
                    <p>Exchange your encryption password with trusted contacts through offline channels - in person, phone call, or encrypted messages. Never send encryption keys through unencrypted channels.</p>
                </div>

                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Unique Key Per Conversation</h3>
                    <p>For maximum security, create a different encryption password for each conversation. This means even if one key is compromised, all other conversations remain secure.</p>
                </div>

                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Local Message Encryption</h3>
                    <p>Your messages are encrypted locally on your device before being transmitted. The plaintext never leaves your device - only encrypted ciphertext travels through the network.</p>
                </div>

                <div class="step">
                    <div class="step-number">5</div>
                    <h3>Secure Server Transit</h3>
                    <p>Encrypted messages pass through our servers but remain completely unreadable. We use TLS/SSL for transport security, adding an extra layer of protection during transmission.</p>
                </div>

                <div class="step">
                    <div class="step-number">6</div>
                    <h3>Optional Unencrypted Mode</h3>
                    <p>Need to send a quick message without encryption? Toggle unencrypted mode for convenience. These messages still benefit from HTTPS/SSL transport security and server-side authentication.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Guarantee Section -->
    <section class="security-guarantee">
        <div class="container">
            <h2 class="section-title">Our Security Guarantee</h2>
            <div class="guarantee-grid">
                <div class="guarantee-item">
                    <div class="guarantee-icon">🛡️</div>
                    <h3>Military-Grade Encryption</h3>
                    <p>AES-256 bit encryption - the same standard used by governments and military organizations worldwide.</p>
                </div>
                <div class="guarantee-item">
                    <div class="guarantee-icon">🔐</div>
                    <h3>Open Source Security</h3>
                    <p>Our encryption code is open source and audited by security experts. Trust through transparency.</p>
                </div>
                <div class="guarantee-item">
                    <div class="guarantee-icon">📵</div>
                    <h3>No Data Collection</h3>
                    <p>We don't track, store, or sell your personal information. Your privacy is not our product.</p>
                </div>
                <div class="guarantee-item">
                    <div class="guarantee-icon">⚖️</div>
                    <h3>Legal Protection</h3>
                    <p>Even under legal pressure, we cannot provide your messages - because we simply don't have them.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Platforms Section -->
    <section id="platforms" class="platforms">
        <div class="container">
            <h2 class="section-title">Available on All Your Devices</h2>
            <p class="section-subtitle">Secure messaging wherever you are, on any platform you prefer. Your encrypted conversations sync seamlessly across all devices.</p>
            
            <div class="platform-grid">
                <div class="platform-card">
                    <div class="platform-icon">🌐</div>
                    <h3>Web Application</h3>
                    <p>Access instantly from any modern browser. No installation required.</p>
                    <a href="https://web.crypt-talk.online/" class="btn btn-primary">Launch Web App</a>
                </div>

                <div class="platform-card">
                    <div class="platform-icon">🤖</div>
                    <h3>Android</h3>
                    <p>Download directly or get it from Google Play Store</p>
                    <a href="{{ asset('downloads/app-release-signed.apk') }}" class="btn btn-primary" download>Download APK</a>
                </div>

                <div class="platform-card">
                    <div class="platform-icon">🍎</div>
                    <h3>iOS</h3>
                    <p>iPhone and iPad support coming to the App Store</p>
                    <span class="coming-soon-badge">Coming Q2 2026</span>
                    <a href="#" class="btn btn-secondary disabled">Coming Soon</a>
                </div>

                <div class="platform-card">
                    <div class="platform-icon">🖥️</div>
                    <h3>Windows & macOS</h3>
                    <p>Native desktop applications for enhanced performance</p>
                    <span class="coming-soon-badge">Coming Q3 2026</span>
                    <a href="#" class="btn btn-secondary disabled">Coming Soon</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing">
        <div class="container">
            <h2 class="section-title">Simple, Transparent Pricing</h2>
            <p class="section-subtitle">Start with our generous free tier or unlock premium features for enhanced productivity and security.</p>
            
            <div class="pricing-grid">
                <div class="pricing-card">
                    <h3>Free Forever</h3>
                    <div class="price">$0<span>/month</span></div>
                    <ul class="pricing-features">
                        <li>✅ Military-grade end-to-end encryption</li>
                        <li>✅ Unlimited encrypted messages</li>
                        <li>✅ Up to 3 encrypted conversations</li>
                        <li>✅ Web & mobile access</li>
                        <li>✅ Community support</li>
                        <li>✅ No credit card required</li>
                    </ul>
                    <a href="https://web.crypt-talk.online/register" class="btn btn-secondary">Get Started Free</a>
                </div>

                <div class="pricing-card featured">
                    <div class="popular-badge">Most Popular</div>
                    <h3>Premium</h3>
                    <div class="price">$4.99<span>/month</span></div>
                    <ul class="pricing-features">
                        <li>✅ Everything in Free plan</li>
                        <li>✅ Unlimited encrypted conversations</li>
                        <li>✅ Group chats up to 50 members</li>
                        <li>✅ Priority 24/7 support</li>
                        <li>✅ File sharing up to 100MB per file</li>
                        <li>✅ Message search & archives</li>
                        <li>✅ Custom encryption algorithms</li>
                        <li>✅ Ad-free experience</li>
                        <li>✅ Advanced security analytics</li>
                    </ul>
                    <a href="https://web.crypt-talk.online/premium" class="btn btn-primary">Upgrade to Premium</a>
                </div>
            </div>
            
            <div class="pricing-note">
                <p>💡 <strong>30-Day Money Back Guarantee</strong> - Try Premium risk-free. Cancel anytime, no questions asked.</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Take Control of Your Privacy?</h2>
            <p>Join thousands of users who trust CryptTalk to keep their conversations secure and private.</p>
            <div class="cta-buttons">
                <a href="https://web.crypt-talk.online/register" class="btn btn-primary btn-large">Start Messaging Securely</a>
                <a href="#how-it-works" class="btn btn-secondary btn-large">Learn More</a>
            </div>
        </div>
    </section>
@endsection