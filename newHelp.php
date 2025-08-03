<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How to Help - SPCA Grahamstown</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Germania+One&family=Hind+Siliguri:wght@300;400;500;600;700&display=swap">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Hind Siliguri', sans-serif;
            background: linear-gradient(135deg, #f8f6f4 0%, #ede5e0 50%, #f0e8e3 100%);
            color: #333;
            line-height: 1.7;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Header */
        header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem 0;
        }

        h1 {
            font-family: 'Hind Siliguri', sans-serif;
            font-size: 3.5rem;
            color: #AE9787;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .intro-text {
            font-size: 1.2rem;
            color: #555;
            line-height: 1.8;
            max-width: 800px;
            margin: 0 auto 2rem auto;
        }

        /* Picture placeholders */
        .image-placeholder {
            margin: 2rem 0;
            padding: 2rem;
            border: 3px dashed #AE9787;
            border-radius: 15px;
            background: rgba(174,151,135,0.05);
            color: #AE9787;
            font-style: italic;
            font-weight: 500;
            text-align: center;
        }

        /* Main content sections - NO BOXES */
        section {
            margin-bottom: 4rem;
            padding: 0;
        }

        section h2 {
            font-family: 'Hind Siliguri', sans-serif;
            font-size: 2.5rem;
            color: #AE9787;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .topic-icon {
            font-size: 2rem;
            color: #AE9787;
        }

        section p {
            color: #666;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        section h3 {
            font-family: 'Hind Siliguri', sans-serif;
            font-size: 1.8rem;
            color: #333;
            margin: 2rem 0 1rem 0;
        }

        section h4 {
            font-weight: 600;
            color: #AE9787;
            margin: 1.5rem 0 0.5rem 0;
            font-size: 1.2rem;
        }

        /* Lists */
        ul {
            margin: 1rem 0;
            padding-left: 0;
            list-style: none;
        }

        ul li {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 0.8rem;
            color: #666;
        }

        ul li::before {
            content: '\2713';
            color: #AE9787;
            font-weight: bold;
            position: absolute;
            left: 0;
            font-size: 1.2rem;
        }

        /* Sponsor pricing inline */
        .sponsor-options {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin: 1.5rem 0;
        }

        .sponsor-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #AE9787;
        }

        .sponsor-item strong {
            color: #AE9787;
            display: block;
            margin-bottom: 0.5rem;
        }

        .sponsor-price {
            font-size: 1.3rem;
            font-weight: bold;
            color: #28a745;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #AE9787 0%, #8B7A68 100%);
            color: #fff;
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            margin: 1rem 0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(174,151,135,0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(174,151,135,0.4);
        }

        /* About section with aside */
        .about-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
            margin-top: 2rem;
        }

        aside {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            border-left: 4px solid #AE9787;
        }

        aside h4 {
            color: #AE9787;
            margin-bottom: 1rem;
        }

        aside p {
            font-size: 0.95rem;
            color: #666;
            margin-bottom: 1rem;
        }

        /* Visit section - highlighted */
        .visit-section {
            background: linear-gradient(135deg, #AE9787 0%, #8B7A68 100%);
            color: #fff;
            text-align: center;
            padding: 3rem;
            border-radius: 15px;
            margin-bottom: 4rem;
        }

        .visit-section h2 {
            color: #fff;
        }

        .visit-section p {
            color: rgba(255,255,255,0.9);
        }

        .visit-section .btn {
            background: #fff;
            color: #AE9787;
        }

        .visit-section .btn:hover {
            background: #f8f9fa;
        }

        /* Contact section */
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            margin-top: 2rem;
        }

        .contact-item h4 {
            font-family: 'Hind Siliguri', sans-serif;
            font-size: 1.5rem;
            color: #AE9787;
            margin-bottom: 1rem;
        }

        .contact-item p {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .hours-list {
            list-style: none;
            padding: 0;
        }

        .hours-list li {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eee;
        }

        .hours-list li::before {
            display: none;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-links a {
            color: #fff;
            background: linear-gradient(135deg, #AE9787 0%, #8B7A68 100%);
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-size: 1.3rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-links a:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(174,151,135,0.4);
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, #333 0%, #222 100%);
            color: #fff;
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
            border-radius: 15px;
        }

        footer p {
            margin: 0.5rem 0;
            opacity: 0.9;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            h1 {
                font-size: 2.5rem;
            }

            .about-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .contact-grid {
                grid-template-columns: 1fr;
            }

            .sponsor-options {
                flex-direction: column;
            }

            .visit-section {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <h1>How to Help</h1>
            <p class="intro-text">At the SPCA, every act of support helps us continue our vital work in caring for animals in need. We offer a variety of ways for you to get involved—whether through a direct donation, sponsoring specific expenses, or even by volunteering your time. Each contribution, big or small, makes a real difference in the lives of our animals and the community we serve. Consider the following ways you can help and be a part of our mission:</p>
            
            <div class="image-placeholder">
                📸 PLACE HERO IMAGE HERE: Large banner image showing happy rescued animals, volunteers caring for pets, or SPCA facility exterior (Recommended size: 1000x400px)
            </div>
        </header>

        <!-- Donate Section -->
        <section id="donate">
            <h2><i class="fas fa-heart topic-icon"></i>Donate</h2>
            
            <div class="image-placeholder">
                📸 PLACE IMAGE HERE: Animals receiving medical care, feeding, or rehabilitation (800x300px)
            </div>
            
            <p>Your financial support is the cornerstone of our ability to provide comprehensive care for animals in need. Every donation, regardless of size, directly impacts the lives of vulnerable animals in our community. Your generosity enables us to provide essential medical treatment, nutritious food, safe shelter, and behavioral rehabilitation for animals who have been abandoned, abused, or neglected.</p>
            
            <p>When you donate to the SPCA Grahamstown, you're not just giving money – you're giving hope. You're funding life-saving surgeries, providing warm bedding for a frightened puppy, ensuring that no animal goes hungry, and supporting our dedicated staff who work tirelessly to give every animal the care they deserve.</p>

            <h3>Sponsor an Expense</h3>
            <p>You can make a targeted impact by sponsoring specific expenses that directly benefit our animals. Choose from these meaningful sponsorship opportunities:</p>
            
            <div class="sponsor-options">
                <div class="sponsor-item">
                    <strong>Pledge Support</strong>
                    <div class="sponsor-price">R100.00</div>
                </div>
                <div class="sponsor-item">
                    <strong>Puppy Pack</strong>
                    <div class="sponsor-price">R950.00</div>
                </div>
                <div class="sponsor-item">
                    <strong>R10 Donation</strong>
                    <div class="sponsor-price">R10.00</div>
                </div>
                <div class="sponsor-item">
                    <strong>Tick & Flea Treatment</strong>
                    <div class="sponsor-price">R100.00</div>
                </div>
            </div>

            <h4>Banking Details:</h4>
            <ul>
                <li><strong>Account Holder:</strong> SPCA Grahamstown</li>
                <li><strong>Bank:</strong> [Bank Name]</li>
                <li><strong>Account No:</strong> [Account Number]</li>
                <li><strong>Reference:</strong> [Donation Reference]</li>
            </ul>

            <a href="#" class="btn">Make a Donation Online</a>
        </section>

        <!-- Volunteer Section -->
        <section id="volunteer">
            <h2><i class="fas fa-hands-helping topic-icon"></i>Volunteer</h2>
            
            <div class="image-placeholder">
                📸 PLACE IMAGE HERE: Volunteers working with animals, cleaning kennels, or at charity shop (800x300px)
            </div>
            
            <p>Make a tangible difference by volunteering your time and skills at the SPCA Grahamstown. Whether you can spare a few hours a week or dedicate regular time to our cause, your contribution is invaluable in helping us care for animals, support adoptions, and run essential community programs.</p>
            
            <p>Our volunteers are the heart of our organization. They provide hands-on care for animals, help with daily operations, assist with fundraising events, and play a crucial role in connecting animals with their forever homes. No matter your background or experience level, there's a way for you to contribute meaningfully to our mission.</p>

            <h3>Volunteer Opportunities Include:</h3>
            <ul>
                <li>Animal care and enrichment activities</li>
                <li>Dog walking and exercise programs</li>
                <li>Assisting with feeding and basic medical care</li>
                <li>Helping at our charity shop</li>
                <li>Administrative support and data entry</li>
                <li>Event planning and fundraising assistance</li>
                <li>Photography for adoption profiles</li>
                <li>Transportation for animals to vet appointments</li>
                <li>Community outreach and education programs</li>
                <li>Maintenance and facility improvement projects</li>
            </ul>

            <h3>SPCA Membership</h3>
            <p>Become an SPCA member today and join a passionate community dedicated to protecting and caring for animals. Your membership not only fuels our mission but also gives you exclusive access to updates, events, and opportunities to make a lasting impact in animal welfare. Members receive regular newsletters, invitations to special events, and the satisfaction of being part of a movement that truly makes a difference.</p>

            <a href="#" class="btn">Apply to Volunteer</a>
        </section>

        <!-- Adopt/Foster Section -->
        <section id="adopt-foster">
            <h2><i class="fas fa-home topic-icon"></i>Adopt/Foster</h2>
            
            <div class="image-placeholder">
                📸 PLACE IMAGE HERE: Happy families with adopted pets, cute adoptable animals waiting for homes (800x300px)
            </div>
            
            <p>Give a rescued animal a loving forever home through adoption, or provide temporary care through our foster program. Every adoption saves two lives – the one you adopt and the one that takes their place in our shelter. Our animals come from various backgrounds, but they all share one thing in common: they're ready to love and be loved.</p>
            
            <p>Our adoption process is designed to ensure the best match between you and your new companion. We provide comprehensive information about each animal's personality, medical history, and specific needs to help you make an informed decision. Our team is here to support you every step of the way, from initial meeting to successful integration into your home.</p>

            <h3>Adoption Services:</h3>
            <ul>
                <li>Comprehensive health checks and vaccinations</li>
                <li>Behavioral assessments and training recommendations</li>
                <li>Spaying/neutering services</li>
                <li>Microchipping for identification</li>
                <li>Post-adoption support and guidance</li>
                <li>Meet-and-greet sessions with potential pets</li>
                <li>Home visits when necessary</li>
                <li>Trial periods for suitable matches</li>
            </ul>

            <h3>Foster Program:</h3>
            <p>If you're not ready for permanent adoption but want to help, consider fostering. Foster families provide temporary homes for animals who need extra care, are too young for adoption, or are recovering from medical treatment. We provide all necessary supplies, medical care, and support – you provide the love and temporary home.</p>

            <a href="#" class="btn">View Available Pets</a>
        </section>

        <!-- Fundraise Section -->
        <section id="fundraise">
            <h2><i class="fas fa-fundraiser topic-icon"></i>Fundraise</h2>
            
            <div class="image-placeholder">
                📸 PLACE IMAGE HERE: Community fundraising events, bake sales, sponsored walks, local supporters (800x300px)
            </div>
            
            <p>Organize fundraising events in your community, workplace, or school to support our mission. From bake sales to sponsored walks, every fundraising effort helps us continue our vital work in animal welfare. Community fundraising not only raises essential funds but also increases awareness about animal welfare issues and helps build a network of supporters.</p>
            
            <p>Fundraising can be fun, creative, and deeply rewarding. Whether you're organizing a small neighborhood event or a large community gathering, every effort makes a difference. We provide support materials, guidance, and resources to help make your fundraising event successful.</p>

            <h3>Fundraising Ideas:</h3>
            <ul>
                <li>Sponsored walks, runs, or cycling events</li>
                <li>Bake sales and food drives</li>
                <li>Car washes and garage sales</li>
                <li>Charity concerts or talent shows</li>
                <li>Online crowdfunding campaigns</li>
                <li>Corporate partnership programs</li>
                <li>Birthday or memorial donation requests</li>
                <li>Social media challenges</li>
                <li>School or workplace collection drives</li>
                <li>Auction events with donated items</li>
            </ul>

            <h4>We Provide:</h4>
            <ul>
                <li>Fundraising starter kits and materials</li>
                <li>Promotional materials and signage</li>
                <li>Guidelines and best practice advice</li>
                <li>Tax-deductible receipt processing</li>
                <li>Recognition and thank you materials</li>
            </ul>

            <a href="#" class="btn">Start Your Fundraising Campaign</a>
        </section>

        <!-- Shop to Support Section -->
        <section id="shop">
            <h2><i class="fas fa-shopping-bag topic-icon"></i>Shop to Support</h2>
            
            <div class="image-placeholder">
                📸 PLACE IMAGE HERE: SPCA charity shop interior, donated items display, happy shoppers browsing (800x300px)
            </div>
            
            <p>Visit our charity shop for fantastic bargains while supporting animals in need. Our shop is filled with donated treasures including clothing, books, household items, and unique finds. Every purchase directly supports our animal care programs, making your shopping trip a meaningful contribution to animal welfare.</p>
            
            <p>The charity shop is more than just a retail space – it's a community hub where animal lovers gather, volunteers contribute their time, and bargain hunters find incredible deals. The shop is entirely run by our dedicated volunteers and relies on generous donations from the community.</p>

            <h3>What You'll Find:</h3>
            <ul>
                <li>Quality second-hand clothing for all ages</li>
                <li>Books, magazines, and educational materials</li>
                <li>Household items and kitchenware</li>
                <li>Toys, games, and children's items</li>
                <li>Furniture and home decor</li>
                <li>Vintage and collectible items</li>
                <li>Seasonal and holiday decorations</li>
                <li>Electronics and small appliances</li>
            </ul>

            <h3>Donate Items:</h3>
            <p>Clean out your closets and donate items you no longer need. We accept clothing, household goods, books, and small furniture in good condition. Your donations not only support our cause but also promote sustainable living by giving items a second life.</p>

            <h4>Donation Guidelines:</h4>
            <ul>
                <li>Items should be clean and in good condition</li>
                <li>No electrical items unless recently tested</li>
                <li>Clothing should be freshly laundered</li>
                <li>Books and magazines in readable condition</li>
                <li>Household items complete and functional</li>
            </ul>

            <a href="#" class="btn">Visit Our Charity Shop</a>
        </section>

        <!-- Legacy Giving Section -->
        <section id="legacy">
            <h2><i class="fas fa-gift topic-icon"></i>Legacy Giving</h2>
            
            <div class="image-placeholder">
                📸 PLACE IMAGE HERE: Peaceful image of older animals being cared for, memorial garden, or legacy donors (800x300px)
            </div>
            
            <p>Leave a lasting legacy for animals by including the SPCA in your will. Legacy gifts ensure that our work continues for future generations of animals in need of care and protection. These gifts, regardless of size, create a permanent impact that extends far beyond your lifetime, ensuring that countless animals will receive the care, love, and second chances they deserve.</p>
            
            <p>Legacy giving allows you to make a significant difference in animal welfare while potentially providing tax benefits for your estate. Whether you choose to leave a specific amount, a percentage of your estate, or particular assets, your legacy gift will be used thoughtfully and effectively to continue our mission.</p>

            <h3>Types of Legacy Gifts:</h3>
            <ul>
                <li>Residual gifts (percentage of your estate)</li>
                <li>Pecuniary gifts (specific monetary amounts)</li>
                <li>Specific gifts (particular assets or property)</li>
                <li>Contingent gifts (if primary beneficiaries predecease you)</li>
                <li>Charitable trusts and annuities</li>
                <li>Life insurance policy donations</li>
                <li>Retirement fund beneficiary designations</li>
            </ul>

            <h3>How Legacy Gifts Help:</h3>
            <ul>
                <li>Fund major facility improvements and expansions</li>
                <li>Establish endowment funds for ongoing care</li>
                <li>Support specialized medical equipment purchases</li>
                <li>Create education and outreach programs</li>
                <li>Ensure long-term sustainability of services</li>
                <li>Fund emergency response capabilities</li>
                <li>Support research into animal welfare improvements</li>
            </ul>

            <p><strong>Professional Guidance:</strong> We recommend consulting with your financial advisor, attorney, or estate planning professional when considering legacy giving. We can provide information about our organization and work with your advisors to ensure your intentions are properly documented.</p>

            <a href="#" class="btn">Learn More About Legacy Giving</a>
        </section>

        <!-- About Section with Aside -->
        <section id="about">
            <h2>About SPCA Grahamstown</h2>
            
            <div class="about-content">
                <article>
                    <p>The Grahamstown SPCA (Makhanda) was founded in 1958 and registered as a Non-Profit Organization (NPO no. 035-579) in 2004. The Society is a non-governmental organization and relies entirely on the financial support of concerned individuals and businesses, as well as income from our Charity Shop and other fundraising activities, to sustain our operational and outreach needs.</p>
                    
                    <p>The Society does not receive any funding from the South African Government or the local Municipality, making community support absolutely essential to our continued operation. We are managed by a dedicated Management Committee who volunteer their time and expertise to ensure effective governance and strategic direction.</p>
                    
                    <p>Our professional staff are employed to run the Kennels, conduct Inspectorate work, and operate the Charity Shop. We warmly welcome volunteers at both the Kennels and the Charity Shop, as they are integral to our daily operations and the quality of care we can provide.</p>
                    
                    <p>The Society is proudly affiliated with the NSPCA and operates in full compliance with the rules and standards of the SPCA movement, ensuring that we maintain the highest standards of animal welfare and professional conduct.</p>
                    
                    <div class="image-placeholder">
                        📸 PLACE IMAGE HERE: SPCA facility exterior, management committee group photo, or historical founding photo (600x400px)
                    </div>
                </article>

                <aside>
                    <h4>Our Mission</h4>
                    <p>To prevent cruelty to animals, provide care and shelter for animals in need, and promote responsible pet ownership in our community.</p>
                    
                    <h4>Our Services</h4>
                    <p>Animal rescue and rehabilitation, adoption services, community education, cruelty investigations, and emergency animal response.</p>
                    
                    <h4>Community Impact</h4>
                    <p>Since 1958, we have rescued, rehabilitated, and rehomed thousands of animals while educating the community about animal welfare.</p>
                </aside>
            </div>
        </section>

        <!-- Visit Section -->
        <section class="visit-section" id="visit">
            <h2>Plan a Visit</h2>
            <p>Plan a visit to the SPCA in Grahamstown and experience the joy of meeting our adorable animals in need of loving homes. Whether you're looking to adopt, volunteer, or simply learn more about our work, our team would be delighted to welcome you. Together, we can make a difference in the lives of animals!</p>
            
            <div class="image-placeholder" style="border-color: #fff; color: #fff; background: rgba(255,255,255,0.1);">
                📸 PLACE IMAGE HERE: Visitors interacting with animals, facility tour, or welcome area (800x300px)
            </div>
            
            <a href="#" class="btn">Schedule Your Visit</a>
        </section>

        <!-- Contact Section -->
        <section id="contact">
            <h2><i class="fas fa-phone topic-icon"></i>Contact & Visit Us</h2>
            
            <div class="contact-grid">
                <div class="contact-item">
                    <h4>Contact Details</h4>
                    <p><strong>Address:</strong> Old Bay Road, Makhanda, 6139</p>
                    <p><strong>Phone:</strong> 046 622 3233</p>
                    <p><strong>Email:</strong> chairperson@spcaght.co.za</p>
                    <p><strong>NPO:</strong> 035-579</p>
                    <p><strong>PBO:</strong> 930014101</p>
                    <p><em>Authorized to issue Section 18A tax certificates for donations received</em></p>
                </div>

                <div class="contact-item">
                    <h4>Opening Hours</h4>
                    <ul class="hours-list">
                        <li><span><strong>Monday to Friday</strong></span><span>9:00am – 4:00pm</span></li>
                        <li><span><strong>Saturday</strong></span><span>9:00am – 12:00pm</span></li>
                        <li><span><strong>Sunday</strong></span><span>9:00am – 11:00am</span></li>
                    </ul>
                </div>

                <div class="contact-item">
                    <h4>Follow Us</h4>
                    <p>Stay connected with our latest news, animal stories, and upcoming events through our social media channels.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    
                    <div class="image-placeholder">
                        📸 PLACE IMAGE HERE: Social media collage or community events photo (350x200px)
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <p>Copyright © 2025 SPCA Grahamstown. All Rights Reserved.</p>
            <p><strong>Supported By:</strong> Community donations and local business partnerships</p>
        </footer>
    </div>
</body>
</html>