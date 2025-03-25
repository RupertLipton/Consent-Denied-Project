<?php
/**
 * Template Name: Homepage Template
 */

get_header(); ?>

<div class="home-container">
    <!-- Main introduction section -->
    <section class="intro-section">
        <div class="content-width">
            <h1>Turn the tables on the parking companies</h1>

            <div class="two-column-layout">
                <div class="benefits-column">
                    <ul class="benefits-list">
                        <li>Say goodbye to huge private land car parking penalty charges</li>
                        <li>These penalties are said to be enforceable because you consented to them in the act of parking – a contract.</li>
                        <li>Register here and ALL private land car park operators will be told, that in future, you don't agree to extortionate excess parking charges</li>
                        <li>You still park and you still pay reasonable charges, but become immune from the exorbitant charges</li>
                    </ul>
                </div>
                
                <div class="form-column">
                    <div class="registration-form">
                        <h2>Register Now</h2>
                        <form id="signup-form">
                            <div class="form-field">
                                <label for="name">NAME:</label>
                                <input type="text" id="name" required>
                            </div>
                            <div class="form-field">
                                <label for="email">EMAIL:</label>
                                <input type="email" id="email" required>
                            </div>
                            <div class="form-field">
                                <label for="address">ADDRESS:</label>
                                <input type="text" id="address" required>
                            </div>
                            <div class="form-field">
                                <label for="mobile">MOBILE NUMBER:</label>
                                <input type="text" id="mobile" required>
                            </div>
                            <div class="form-field">
                                <label for="car-reg">CAR REG:</label>
                                <input type="text" id="car-reg" required>
                            </div>
                            <div class="form-field">
                                <label for="car-make">CAR MAKE:</label>
                                <input type="text" id="car-make" required>
                            </div>
                            <div class="form-field">
                                <label for="car-model">CAR MODEL:</label>
                                <input type="text" id="car-model" required>
                            </div>
                            <div class="form-field">
                                <label for="car-color">CAR COLOUR:</label>
                                <input type="text" name="color" id="car-color" required>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="subscription-options">
                <div class="subscription-option core" data-product-id="846">
                    <h3>Core Subscription</h3>
                    <p>£5 - one year</p>
                    <ul>
                        <li>We tell all parking operators you do not consent</li>
                        <li>One vehicle only</li>
                    </ul>
                    <a href="http://consent-denied.co.uk/checkout/?add-to-cart=846" class="subscribe-button" id="core-subscribe-button">Subscribe</a>
                </div>
                
                <div class="subscription-option enhanced" data-product-id="847">
                    <h3>Enhanced Subscription</h3>
                    <p>£30 - two years</p>
                    <ul>
                        <li>We tell all parking operators you do not consent</li>
                        <li>Unlimited number of vehicles</li>
                        <li>Legal correspondence support</li>
                        <li>£10 paid into your legal fighting fund</li>
                    </ul>
                    <a href="/checkout/?add-to-cart=847" class="action-button" id="enhanced-subscribe-button">Subscribe</a>
                </div>
            </div>
            
            <div class="explainer-section">
                <p>The underlying problem with private land parking in the UK is that many operators (probably most) can only make profits by applying as many penalty charges as they can, as at high a level as they can. Sound fair?</p>

                <p>The whole industry rests on the legal concept of implied consent – by seeing (and not even necessarily reading) their signs and then parking your vehicle, you have agreed to the £100 (or more) excess charges.</p>

                <p>But what if you override that implied consent by actively NOT consenting?</p>

                <p>With one click, inform all operators of private car parks, that going forward, you DON'T consent to the terms and conditions on their signage.</p>

                <p>Better than that you offer your contractual terms and if the operator allows you to park, they have consented to your terms. Of course, your terms don't include the high penalty charges.</p>

                <p>This is all about the fundamental concept of "offer and acceptance" in ANY contract. You have explicitly rejected their offered terms and made a counteroffer. Just as they can argue that drivers accept terms just by seeing signs and parking, you can show that in the knowledge of your counteroffer, if they let you access the land and park, they have accepted your terms.</p>

                <p>Of course the law in this regard is not simple, so <a href="#legal-analysis" class="learn-more">click here</a> for a more detailed analysis.</p>
            </div>

            <?php
            // Include Gutenberg content
            if ( have_posts() ) : 
                while ( have_posts() ) : the_post(); 
                    the_content();
                endwhile; 
            endif; 
            ?>
            
        </div>
    </section>
</div>

<style>
/* Two-column layout styles */
.two-column-layout {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    margin-bottom: 30px;
}

.benefits-column, .form-column {
    flex: 1;
    min-width: 300px;
}

.benefits-list {
    padding-left: 20px;
}

.benefits-list li {
    margin-bottom: 15px;
}

.registration-form {
    background-color: #F5F5F5;
    border-radius: 10px;
    padding: 20px;
}

.form-field {
    margin-bottom: 15px;
}

.form-field label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-field input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

/* Call-to-Action Button */
.button-container {
    text-align: center;
    margin-top: 20px;
}

.cta-button {
    display: inline-block;
    background-color: #FF180F; /* Bright Red */
    color: #FFFFFF;
    padding: 1rem 2rem;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 1.1rem;
}

.cta-button:hover {
    background-color: #D70000; /* Slightly darker red */
}

/* Explainer section */
.explainer-section {
    margin-top: 40px;
}

.explainer-section p {
    margin-bottom: 20px;
    line-height: 1.6;
}

/* Subscription button styles */
.subscribe-button, .action-button {
    display: inline-block;
    background-color: #d40000;
    color: white;
    font-weight: bold;
    padding: 12px 30px;
    border-radius: 5px;
    text-decoration: none;
    margin-top: 20px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.subscribe-button:hover, .action-button:hover {
    background-color: #b30000;
    color: white;
}

/* Style for disabled buttons */
.subscribe-button.disabled, .action-button.disabled {
    background-color: #cccccc;
    color: #666666;
    cursor: not-allowed;
}

.subscribe-button.disabled:hover, .action-button.disabled:hover {
    background-color: #cccccc;
    color: #666666;
}
</style>

<?php get_footer(); ?>