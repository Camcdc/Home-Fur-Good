<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How You Can Help - Home Fur Good</title>
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Germania+One&family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Hind Siliguri', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f8f6f4 0%, #ede5e0 50%, #f0e8e3 100%);
            padding-top: 70px;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Decorative background elements */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(174, 151, 135, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(174, 151, 135, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(174, 151, 135, 0.03) 0%, transparent 50%);
            z-index: -1;
        }



        .help-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 70px);
            position: relative;
        }

        /* Decorative container background */
        .help-container::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(174, 151, 135, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            z-index: -1;
        }

        .help-dropdown {
            position: relative;
        }

        .help-dropdown > a {
            display: flex;
            align-items: center;
            color: #AE9787;
            text-align: center;
            text-decoration: none;
            font-size: 2.2rem;
            padding: 1.5rem 3rem;
            font-weight: 600;
            font-family: 'Hind Siliguri', sans-serif;
            background: white;
            border-radius: 15px;
            box-shadow: 
                0px 8px 25px rgba(174, 151, 135, 0.2),
                0px 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: gentlePulse 3s ease-in-out infinite;
        }

        /* Paw print icon before text */
        .help-dropdown > a::before {
            content: '🐾';
            margin-right: 1rem;
            font-size: 1.8rem;
            animation: wiggle 2s ease-in-out infinite;
        }

        .help-dropdown > a::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.8s;
        }

        .help-dropdown > a:hover {
            background: linear-gradient(135deg, #AE9787 0%, #977f6f 100%);
            color: white;
            transform: translateY(-5px) scale(1.05);
            box-shadow: 
                0px 15px 35px rgba(174, 151, 135, 0.4),
                0px 8px 15px rgba(0, 0, 0, 0.2),
                0px 0px 0px 3px rgba(174, 151, 135, 0.2);
        }

        .help-dropdown > a:hover::after {
            left: 100%;
        }

        @keyframes gentlePulse {
            0%, 100% {
                box-shadow: 
                    0px 8px 25px rgba(174, 151, 135, 0.2),
                    0px 4px 10px rgba(0, 0, 0, 0.1);
            }
            50% {
                box-shadow: 
                    0px 12px 30px rgba(174, 151, 135, 0.3),
                    0px 6px 15px rgba(0, 0, 0, 0.15);
            }
        }

        @keyframes wiggle {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-5deg); }
            75% { transform: rotate(5deg); }
        }

        .help-dropdown-content {
            position: absolute;
            top: 100%;
            left: 0;
            background: linear-gradient(145deg, #AE9787 0%, #9d897a 100%);
            color: white;
            min-width: 250px;
            box-shadow: 
                0px 15px 35px rgba(0,0,0,0.3),
                0px 8px 15px rgba(174, 151, 135, 0.2);
            z-index: 1001;
            flex-direction: column;
            border-radius: 0 0 15px 15px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            backdrop-filter: blur(10px);
        }

        .help-dropdown-content a {
            float: none;
            color: white;
            padding: 18px 25px;
            text-decoration: none;
            display: flex;
            align-items: center;
            text-align: left;
            font-size: 1.1rem;
            font-weight: 500;
            font-family: 'Hind Siliguri', sans-serif;
            transition: all 0.5s ease;
            position: relative;
            overflow: hidden;
        }

        /* Icons for each dropdown item */
        .help-dropdown-content a:nth-child(1)::before { content: '💝'; margin-right: 12px; }
        .help-dropdown-content a:nth-child(2)::before { content: '🙋‍♂️'; margin-right: 12px; }
        .help-dropdown-content a:nth-child(3)::before { content: '🏠'; margin-right: 12px; }
        .help-dropdown-content a:nth-child(4)::before { content: '💰'; margin-right: 12px; }
        .help-dropdown-content a:nth-child(5)::before { content: '🛍️'; margin-right: 12px; }
        .help-dropdown-content a:nth-child(6)::before { content: '📜'; margin-right: 12px; }

        .help-dropdown-content a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.5);
            transition: width 0.5s ease;
        }

        .help-dropdown-content a:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(10px);
            padding-left: 35px;
        }

        .help-dropdown-content a:hover::after {
            width: 100%;
        }

        .help-dropdown:hover .help-dropdown-content {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: auto;
        }
    </style>
</head>
<body>
    <!-- You can include your navbar.php here when integrating -->
    
    <div class="help-container">
        <div class="help-dropdown">
            <a href="#">How to Help <i class="fa fa-caret-down"></i></a>
            <div class="help-dropdown-content">
                <a href="donate.php">Donate</a>
                <a href="volunteer.php">Volunteer</a>
                <a href="adopt.php">Adopt/Foster</a>
                <a href="fundraise.php">Fundraise</a>
                <a href="shop.php">Shop to Support</a>
                <a href="legacy.php">Legacy Giving</a>
            </div>
        </div>
    </div>
</body>
</html>