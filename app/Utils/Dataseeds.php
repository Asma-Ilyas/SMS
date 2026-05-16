<?php

namespace App\Utils;

class DataSeed {

    // 🔹 SCHOOLS
    public static $SCHOOLS = [
        ['id'=>1, 'name'=>'Demo School', 'slug'=>'demo-school', 'logo'=>'images/logo.jpg'],
        ['id'=>2, 'name'=>'Superior College Lahore', 'slug'=>'superior-college-lahore', 'logo'=>'images/superiorlogo.jpg'],
    ];

    // 🔹 PAGES
    public static $PAGES = [
        ['id'=>1, 'school_id'=>1, 'title'=>'Home', 'slug'=>'home'],
        ['id'=>2, 'school_id'=>2, 'title'=>'Home', 'slug'=>'home-superior'],
        ['id'=>3, 'school_id'=>2, 'title'=>'About Us', 'slug'=>'about-us-superior'],
        ['id'=>4, 'school_id'=>2, 'title'=>'Admission Form', 'slug'=>'admission-form'],
        ['id'=>5, 'school_id'=>2, 'title'=>'Classes', 'slug'=>'classes'],
    ];

    // 🔹 SECTION TYPES
    public static $SECTION_TYPES = [
        ['id'=>1,'name'=>'Hero Section','slug'=>'hero'],
        ['id'=>2,'name'=>'Slider','slug'=>'slider'],
        ['id'=>3,'name'=>'Mission','slug'=>'mission'],
        ['id'=>4,'name'=>'About','slug'=>'about'],
        ['id'=>5,'name'=>'Stats','slug'=>'stats'],
        ['id'=>6,'name'=>'Gallery','slug'=>'gallery'],
        ['id'=>7,'name'=>'Contact','slug'=>'contact'],
        ['id'=>8,'name'=>'Programs','slug'=>'programs'],
        ['id'=>9,'name'=>'Admission CTA','slug'=>'admission_cta'],
        ['id'=>10,'name'=>'Footer','slug'=>'footer'],
        ['id'=>11,'name'=>'Copyright','slug'=>'copyright'],
        ['id'=>12,'name'=>'Form Section','slug'=>'form'],
        ['id'=>13,'name'=>'FAQ Section','slug'=>'faq'],
    ];

    // 🔹 SECTIONS
    public static $SECTIONS = [
        // ================= OLD PAGE (Demo School) =================
        ['id'=>1,'page_id'=>1,'section_type_id'=>1,'sort_order'=>1,'is_active'=>true],
        ['id'=>2,'page_id'=>1,'section_type_id'=>2,'sort_order'=>2,'is_active'=>true],
        ['id'=>3,'page_id'=>1,'section_type_id'=>3,'sort_order'=>3,'is_active'=>true],
        ['id'=>4,'page_id'=>1,'section_type_id'=>4,'sort_order'=>4,'is_active'=>true],
        ['id'=>5,'page_id'=>1,'section_type_id'=>5,'sort_order'=>5,'is_active'=>true],
        ['id'=>6,'page_id'=>1,'section_type_id'=>6,'sort_order'=>6,'is_active'=>true],
        ['id'=>7,'page_id'=>1,'section_type_id'=>7,'sort_order'=>7,'is_active'=>true],

        // ================= HOME PAGE (Superior College) =================
        ['id'=>8,'page_id'=>2,'section_type_id'=>1,'sort_order'=>1,'is_active'=>true],
        ['id'=>9,'page_id'=>2,'section_type_id'=>4,'sort_order'=>2,'is_active'=>true],
        ['id'=>10,'page_id'=>2,'section_type_id'=>8,'sort_order'=>3,'is_active'=>true],
        ['id'=>11,'page_id'=>2,'section_type_id'=>9,'sort_order'=>4,'is_active'=>true],
        ['id'=>12,'page_id'=>2,'section_type_id'=>10,'sort_order'=>5,'is_active'=>true],
        ['id'=>13,'page_id'=>2,'section_type_id'=>11,'sort_order'=>6,'is_active'=>true],

        // ================= ABOUT US PAGE (Superior College) =================
        ['id'=>14,'page_id'=>3,'section_type_id'=>1,'sort_order'=>1,'is_active'=>true],
        ['id'=>15,'page_id'=>3,'section_type_id'=>4,'sort_order'=>2,'is_active'=>true],
        ['id'=>16,'page_id'=>3,'section_type_id'=>8,'sort_order'=>3,'is_active'=>true],
        ['id'=>17,'page_id'=>3,'section_type_id'=>9,'sort_order'=>4,'is_active'=>true],
        ['id'=>18,'page_id'=>3,'section_type_id'=>10,'sort_order'=>5,'is_active'=>true],
        ['id'=>19,'page_id'=>3,'section_type_id'=>11,'sort_order'=>6,'is_active'=>true],

        // ================= ADMISSION FORM PAGE (Superior College) =================
        ['id'=>20,'page_id'=>4,'section_type_id'=>1,'sort_order'=>1,'is_active'=>true],
        ['id'=>21,'page_id'=>4,'section_type_id'=>12,'sort_order'=>2,'is_active'=>true],
        ['id'=>22,'page_id'=>4,'section_type_id'=>10,'sort_order'=>3,'is_active'=>true],
        ['id'=>23,'page_id'=>4,'section_type_id'=>11,'sort_order'=>4,'is_active'=>true],

        // ================= CLASSES PAGE (Superior College) – General Classes Listing =================
        ['id'=>24,'page_id'=>5,'section_type_id'=>1,'sort_order'=>1,'is_active'=>true], // hero
        ['id'=>25,'page_id'=>5,'section_type_id'=>4,'sort_order'=>2,'is_active'=>true], // about (introduction)
        ['id'=>26,'page_id'=>5,'section_type_id'=>8,'sort_order'=>3,'is_active'=>true], // programs (6 cards)
        ['id'=>27,'page_id'=>5,'section_type_id'=>4,'sort_order'=>4,'is_active'=>true], // about (eligibility)
        ['id'=>28,'page_id'=>5,'section_type_id'=>9,'sort_order'=>5,'is_active'=>true], // admission cta
        ['id'=>29,'page_id'=>5,'section_type_id'=>13,'sort_order'=>6,'is_active'=>true], // faq
        ['id'=>30,'page_id'=>5,'section_type_id'=>10,'sort_order'=>7,'is_active'=>true], // footer
        ['id'=>31,'page_id'=>5,'section_type_id'=>11,'sort_order'=>8,'is_active'=>true], // copyright
    ];

    // 🔹 SECTION FIELDS (unchanged from original)
    public static $SECTION_FIELDS = [
        // HERO
        ['id'=>1,'section_type_id'=>1,'name'=>'heading','field_type'=>'text'],
        ['id'=>2,'section_type_id'=>1,'name'=>'subheading','field_type'=>'text'],
        ['id'=>3,'section_type_id'=>1,'name'=>'image','field_type'=>'image'],
        ['id'=>22,'section_type_id'=>1,'name'=>'campus','field_type'=>'text'],
        ['id'=>23,'section_type_id'=>1,'name'=>'logo','field_type'=>'image'],
        ['id'=>32,'section_type_id'=>1,'name'=>'style','field_type'=>'json'],

        // SLIDER
        ['id'=>4,'section_type_id'=>2,'name'=>'images','field_type'=>'json'],

        // MISSION
        ['id'=>5,'section_type_id'=>3,'name'=>'text','field_type'=>'textarea'],

        // ABOUT
        ['id'=>49,'section_type_id'=>4,'name'=>'content','field_type'=>'json'],
        ['id'=>33,'section_type_id'=>4,'name'=>'style','field_type'=>'json'],

        // STATS
        ['id'=>8,'section_type_id'=>5,'name'=>'students','field_type'=>'text'],
        ['id'=>9,'section_type_id'=>5,'name'=>'teachers','field_type'=>'text'],
        ['id'=>10,'section_type_id'=>5,'name'=>'classes','field_type'=>'text'],

        // GALLERY
        ['id'=>11,'section_type_id'=>6,'name'=>'images','field_type'=>'json'],

        // CONTACT
        ['id'=>12,'section_type_id'=>7,'name'=>'address','field_type'=>'text'],
        ['id'=>13,'section_type_id'=>7,'name'=>'phone','field_type'=>'text'],
        ['id'=>14,'section_type_id'=>7,'name'=>'email','field_type'=>'text'],
        ['id'=>39,'section_type_id'=>7,'name'=>'style','field_type'=>'json'],

        // PROGRAMS
        ['id'=>15,'section_type_id'=>8,'name'=>'title','field_type'=>'text'],
        ['id'=>35,'section_type_id'=>8,'name'=>'style','field_type'=>'json'],
        ['id'=>48,'section_type_id'=>8,'name'=>'cards','field_type'=>'json'],

        // ADMISSION CTA
        ['id'=>18,'section_type_id'=>9,'name'=>'heading','field_type'=>'text'],
        ['id'=>19,'section_type_id'=>9,'name'=>'subheading','field_type'=>'text'],
        ['id'=>20,'section_type_id'=>9,'name'=>'button_text','field_type'=>'text'],
        ['id'=>21,'section_type_id'=>9,'name'=>'image','field_type'=>'image'],
        ['id'=>34,'section_type_id'=>9,'name'=>'style','field_type'=>'json'],

        // FOOTER
        ['id'=>25,'section_type_id'=>10,'name'=>'logo','field_type'=>'image'],
        ['id'=>26,'section_type_id'=>10,'name'=>'description','field_type'=>'textarea'],
        ['id'=>27,'section_type_id'=>10,'name'=>'phone','field_type'=>'text'],
        ['id'=>28,'section_type_id'=>10,'name'=>'email','field_type'=>'text'],
        ['id'=>29,'section_type_id'=>10,'name'=>'address','field_type'=>'text'],
        ['id'=>40,'section_type_id'=>10,'name'=>'social_links','field_type'=>'json'],
        ['id'=>37,'section_type_id'=>10,'name'=>'style','field_type'=>'json'],

        // COPYRIGHT
        ['id'=>30,'section_type_id'=>11,'name'=>'text','field_type'=>'text'],
        ['id'=>31,'section_type_id'=>11,'name'=>'links','field_type'=>'json'],
        ['id'=>38,'section_type_id'=>11,'name'=>'style','field_type'=>'json'],

        // FORM SECTION
        ['id'=>50,'section_type_id'=>12,'name'=>'title','field_type'=>'text'],
        ['id'=>51,'section_type_id'=>12,'name'=>'form_config','field_type'=>'json'],
        ['id'=>52,'section_type_id'=>12,'name'=>'style','field_type'=>'json'],

        // FAQ SECTION
        ['id'=>53,'section_type_id'=>13,'name'=>'title','field_type'=>'text'],
        ['id'=>54,'section_type_id'=>13,'name'=>'faq_items','field_type'=>'json'],
        ['id'=>55,'section_type_id'=>13,'name'=>'style','field_type'=>'json'],
    ];

    // 🔹 SECTION FIELD VALUES
    public static $SECTION_FIELD_VALUES = [
        // ===== OLD PAGE (Demo School) – unchanged =====
        ['section_id'=>1,'field_id'=>1,'value'=>'Welcome to Demo School'],
        ['section_id'=>1,'field_id'=>2,'value'=>'Building Future Leaders'],
        ['section_id'=>1,'field_id'=>3,'value'=>'images/hero.jpg'],
        ['section_id'=>2,'field_id'=>4,'value'=>'["images/slide1.jpg","images/slide2.jpg","images/slide3.jpg"]'],
        ['section_id'=>3,'field_id'=>5,'value'=>'Our mission is to provide quality education.'],
        ['section_id'=>4,'field_id'=>49,'value'=>'{"blocks":[{"type":"text","content":"About Our School"},{"type":"text","content":"We are committed to excellence."}]}'],
        ['section_id'=>4,'field_id'=>33,'value'=>'{"padding":"40px","background_color":"#f9f9f9","text_color":"#333","alignment":"left"}'],
        ['section_id'=>5,'field_id'=>8,'value'=>'1200 Students'],
        ['section_id'=>5,'field_id'=>9,'value'=>'80 Teachers'],
        ['section_id'=>5,'field_id'=>10,'value'=>'40 Classes'],
        ['section_id'=>6,'field_id'=>11,'value'=>'["images/g1.jpg","images/g2.jpg","images/g3.jpg","images/g4.jpg","images/g5.jpg","images/g6.jpg"]'],
        ['section_id'=>7,'field_id'=>12,'value'=>'Main Road'],
        ['section_id'=>7,'field_id'=>13,'value'=>'+92 300'],
        ['section_id'=>7,'field_id'=>14,'value'=>'info@test.com'],

        // ===== HOME PAGE (Superior College) – unchanged =====
        ['section_id'=>8,'field_id'=>22,'value'=>'Lahore Main Campus'],
        ['section_id'=>8,'field_id'=>1,'value'=>'Your Path towards Success Begins at Superior'],
        ['section_id'=>8,'field_id'=>2,'value'=>'Excellence in Education for Every Student'],
        ['section_id'=>8,'field_id'=>23,'value'=>'images/superiorlogo.jpg'],
        ['section_id'=>8,'field_id'=>3,'value'=>'images/superiorhero.jpg'],
        ['section_id'=>8,'field_id'=>32,'value'=>'{
            "overlay_color": "rgba(0, 100, 0, 0.5)",
            "blur": "8px",
            "text_align": "center",
            "text_position": "absolute",
            "text_top": "50%",
            "text_left": "50%",
            "transform": "translate(-50%, -50%)",
            "text_color": "#ffffff"
        }'],

        ['section_id'=>9,'field_id'=>49,'value'=>'{
            "blocks": [
                {
                    "type": "text",
                    "content": "Superior College Lahore is a landmark institute of quality education, offering a home-like and feasible study environment, world-class college facilities, and expert trainers to help you not only become educationally qualified but also gain lifelong values. Situated at the central location of Kalma Chowk, the Lahore campus is very easily reachable from any point or distance in the city.\n\nThe study culture at Superior College Lahore is reflective of pure Superior values and mission of ‘facilitating Superior human beings’. This is a place where students learn the art of living wholeheartedly and with a ‘sense of purpose’. ‘Excellence’ is achieved in academics, co-curricular, and recreational activities. ‘Team work’ is learnt through sports and team building adventurous tours to the national and international destinations. ‘Creativity & innovation’ are enhanced through opportunities to work on skill-based projects, while ‘reward for performance’ is ensured with every success to boost a passion for winning.\n\nCollege students know that their teachers care about them, which makes them feel honored, protected, and valued. Superior guides its students to go confidently in the direction of their dreams and empowers them with the spirit to live the life they had only imagined."
                },
                {
                    "type": "image",
                    "src": "images/superiorabout.jpg",
                    "alt": "Superior College Campus",
                    "position": "right"
                }
            ]
        }'],
        ['section_id'=>9,'field_id'=>33,'value'=>'{
            "padding": "60px",
            "background_color": "#ffffff",
            "text_color": "black",
            "alignment": "left"
        }'],

        ['section_id'=>10,'field_id'=>15,'value'=>'Our Academic Programs'],
        ['section_id'=>10,'field_id'=>48,'value'=>'[
            {
                "title": "📚 PG to 8th Standard",
                "type": "list",
                "items": ["Play Group", "Nursery", "Prep", "1st Standard", "2nd Standard", "3rd Standard", "4th Standard", "5th Standard", "6th Standard", "7th Standard", "8th Standard"],
                "color": "#FF8C00"
            },
            {
                "title": "🎓 9th & 10th Classes",
                "type": "grouped",
                "groups": {
                    "🔬 Science Group": ["Physics", "Chemistry", "Biology", "Mathematics", "Computer Science"],
                    "🎨 Arts Group": ["Economics", "Sociology", "Psychology", "Islamic Studies", "English Literature"]
                },
                "color": "#006400"
            }
        ]'],
        ['section_id'=>10,'field_id'=>35,'value'=>'{
            "background_color": "#f8f9fa",
            "padding": "60px 20px",
            "alignment": "center",
            "card_border_radius": "12px",
            "card_shadow": "0 8px 20px rgba(0,0,0,0.1)"
        }'],

        ['section_id'=>11,'field_id'=>18,'value'=>'ADMISSIONS OPEN'],
        ['section_id'=>11,'field_id'=>19,'value'=>'Join Superior and be a part of progressing future'],
        ['section_id'=>11,'field_id'=>20,'value'=>'ENROLL HERE!'],
        ['section_id'=>11,'field_id'=>21,'value'=>'images/superioraddmision.jpg'],
        ['section_id'=>11,'field_id'=>34,'value'=>'{
            "padding": "80px 20px",
            "background_image": "images/superioraddmision.jpg",
            "background_size": "cover",
            "background_position": "center",
            "text_color": "#ffffff",
            "alignment": "center",
            "button_background": "#ffffff",
            "button_text_color": "#0033cc",
            "button_border_radius": "50px",
            "form_page_slug": "admission-form"
        }'],

        ['section_id'=>12,'field_id'=>25,'value'=>'images/superiorlogo.jpg'],
        ['section_id'=>12,'field_id'=>26,'value'=>'We are committed to transform the lives of students, faculty and staff by providing them a Superior learning experience. Our plan EQ & IQ enables them to lead a meaningful & rewarding life.'],
        ['section_id'=>12,'field_id'=>27,'value'=>'042-111-00-00-78'],
        ['section_id'=>12,'field_id'=>28,'value'=>'info@superiorcolleges.edu.pk'],
        ['section_id'=>12,'field_id'=>29,'value'=>'The Superior Group of Colleges, 31 Tipu block New Garden Town Lahore'],
        ['section_id'=>12,'field_id'=>40,'value'=>'[
            {"platform":"facebook-f", "url":"https://facebook.com/superiorcollege"},
            {"platform":"instagram", "url":"https://instagram.com/superiorcollege"},
            {"platform":"linkedin", "url":"https://linkedin.com/school/superiorcollege"},
            {"platform":"youtube", "url":"https://youtube.com/superiorcollege"}
        ]'],
        ['section_id'=>12,'field_id'=>37,'value'=>'{
            "background_color": "#006400",
            "text_color": "#ffffff",
            "link_color": "#cccccc",
            "padding": "50px 20px 30px",
            "logo_width": "120px"
        }'],

        ['section_id'=>13,'field_id'=>30,'value'=>'© Copyright Superior Group All Rights Reserved 2026'],
        ['section_id'=>13,'field_id'=>38,'value'=>'{
            "background_color": "orange",
            "text_color": "#ffffff",
            "link_color": "#cccccc",
            "padding": "12px 20px",
            "alignment": "center",
            "font_size": "0.9rem"
        }'],

        // ===== ABOUT US PAGE (Superior College) – unchanged =====
        ['section_id'=>14,'field_id'=>22,'value'=>'Lahore Main Campus'],
        ['section_id'=>14,'field_id'=>1,'value'=>'About Superior College Lahore'],
        ['section_id'=>14,'field_id'=>2,'value'=>'Excellence in Education for Every Student'],
        ['section_id'=>14,'field_id'=>23,'value'=>'images/superiorlogo.jpg'],
        ['section_id'=>14,'field_id'=>3,'value'=>'images/superiorhero.jpg'],
        ['section_id'=>14,'field_id'=>32,'value'=>'{
            "overlay_color": "rgba(0, 100, 0, 0.5)",
            "blur": "8px",
            "text_align": "center",
            "text_position": "absolute",
            "text_top": "50%",
            "text_left": "50%",
            "transform": "translate(-50%, -50%)",
            "text_color": "#ffffff"
        }'],

        ['section_id'=>15,'field_id'=>49,'value'=>'{
            "blocks": [
                {
                    "type": "text",
                    "content": "Why Choose Superior Colleges"
                },
                {
                    "type": "text",
                    "content": "We are committed to enhancing the potentials of students, faculty, staff, and all segments of our society by bringing a positive change in their personal and career lives, motivating them for self-enlightenment through Quality Education, Personality Development, True Professionalism, and Career Planning; thus, adding value to our nation, and ultimately to humanity!"
                },
                {
                    "type": "list",
                    "items": ["Intermediate Programs", "Associate Programs", "Undergraduate Programs", "Student LMS", "Modern Infrastructure", "In-House Labs"]
                },
                {
                    "type": "images",
                    "srcs": ["images/aboutpage1.jpg", "images/aboutpage2.jpg"],
                    "layout": "side-by-side"
                }
            ]
        }'],
        ['section_id'=>15,'field_id'=>33,'value'=>'{
            "padding": "60px",
            "background_color": "#f5f5f5",
            "text_color": "#333",
            "alignment": "left"
        }'],

        ['section_id'=>16,'field_id'=>15,'value'=>'Our Vision, Mission & Values'],
        ['section_id'=>16,'field_id'=>48,'value'=>'[
            {
                "title": "🎯 Our Vision",
                "type": "text",
                "content": "To become the first choice of students and a Superior educational institute of Pakistan.",
                "color": "#FF8C00"
            },
            {
                "title": "🚀 Our Mission",
                "type": "text",
                "content": "We are committed to transform the lives of students, faculty and staff by providing them a Superior learning experience. Our plan EQ & IQ enables them to lead a meaningful & rewarding life.",
                "color": "#006400"
            },
            {
                "title": "💎 Our Values",
                "type": "list",
                "items": ["Student Success", "Excellence", "Innovation & Creativity", "Team Work", "Agility", "Fairness"],
                "color": "#0033cc"
            }
        ]'],
        ['section_id'=>16,'field_id'=>35,'value'=>'{
            "background_color": "#ffffff",
            "padding": "60px 20px",
            "alignment": "center",
            "card_border_radius": "12px",
            "card_shadow": "0 8px 20px rgba(0,0,0,0.1)"
        }'],

        ['section_id'=>17,'field_id'=>18,'value'=>'Spread over 130+ Campuses'],
        ['section_id'=>17,'field_id'=>19,'value'=>'We Are One Of The Largest Education Network In Pakistan. We are committed to enhancing the potentials of students, faculty, staff, and all segments of our society by bringing a positive change in their personal and career lives, motivating them for self-enlightenment through Quality Education, Personality Development, True Professionalism, and Career Planning; thus, adding value to our nation, and ultimately to humanity!'],
        ['section_id'=>17,'field_id'=>20,'value'=>'Explore Campuses'],
        ['section_id'=>17,'field_id'=>21,'value'=>'images/map.jpg'],
        ['section_id'=>17,'field_id'=>34,'value'=>'{
            "padding": "80px 20px",
            "background_color": "#f0f7ff",
            "text_color": "#0033cc",
            "alignment": "left",
            "button_background": "#0033cc",
            "button_text_color": "#ffffff",
            "button_border_radius": "50px",
            "form_page_slug": "admission-form"
        }'],

        ['section_id'=>18,'field_id'=>25,'value'=>'images/superiorlogo.jpg'],
        ['section_id'=>18,'field_id'=>26,'value'=>'We are committed to transform the lives of students, faculty and staff by providing them a Superior learning experience. Our plan EQ & IQ enables them to lead a meaningful & rewarding life.'],
        ['section_id'=>18,'field_id'=>27,'value'=>'042-111-00-00-78'],
        ['section_id'=>18,'field_id'=>28,'value'=>'info@superiorcolleges.edu.pk'],
        ['section_id'=>18,'field_id'=>29,'value'=>'The Superior Group of Colleges, 31 Tipu block New Garden Town Lahore'],
        ['section_id'=>18,'field_id'=>40,'value'=>'[
            {"platform":"facebook-f", "url":"https://facebook.com/superiorcollege"},
            {"platform":"instagram", "url":"https://instagram.com/superiorcollege"},
            {"platform":"linkedin", "url":"https://linkedin.com/school/superiorcollege"},
            {"platform":"youtube", "url":"https://youtube.com/superiorcollege"}
        ]'],
        ['section_id'=>18,'field_id'=>37,'value'=>'{
            "background_color": "#006400",
            "text_color": "#ffffff",
            "link_color": "#cccccc",
            "padding": "50px 20px 30px",
            "logo_width": "120px"
        }'],

        ['section_id'=>19,'field_id'=>30,'value'=>'© Copyright Superior Group All Rights Reserved 2026'],
        ['section_id'=>19,'field_id'=>38,'value'=>'{
            "background_color": "orange",
            "text_color": "#ffffff",
            "link_color": "#cccccc",
            "padding": "12px 20px",
            "alignment": "center",
            "font_size": "0.9rem"
        }'],

        // ===== ADMISSION FORM PAGE (Superior College) – unchanged =====
        ['section_id'=>20,'field_id'=>22,'value'=>'Admission Open 2026'],
        ['section_id'=>20,'field_id'=>1,'value'=>'Apply for Admission'],
        ['section_id'=>20,'field_id'=>2,'value'=>'Fill the form below to start your journey with Superior College'],
        ['section_id'=>20,'field_id'=>23,'value'=>'images/superiorlogo.jpg'],
        ['section_id'=>20,'field_id'=>3,'value'=>'images/superiorhero.jpg'],
        ['section_id'=>20,'field_id'=>32,'value'=>'{
            "overlay_color": "rgba(0, 100, 0, 0.5)",
            "blur": "8px",
            "text_align": "center",
            "text_position": "absolute",
            "text_top": "50%",
            "text_left": "50%",
            "transform": "translate(-50%, -50%)",
            "text_color": "#ffffff"
        }'],

        ['section_id'=>21,'field_id'=>50,'value'=>'Admission Application Form'],
        ['section_id'=>21,'field_id'=>51,'value'=>'{
            "fields": [
                {"name":"full_name","label":"Your Name","type":"text","required":true,"placeholder":"Enter your full name"},
                {"name":"gender","label":"Gender","type":"select","options":["Male","Female","Other"],"required":true},
                {"name":"email","label":"Email","type":"email","required":true,"placeholder":"you@example.com"},
                {"name":"phone","label":"Phone","type":"tel","required":true,"placeholder":"+92 XXX XXXXXXX"},
                {"name":"dob","label":"Date of Birth","type":"date"},
                {"name":"city_campus","label":"City / Campus","type":"text","required":true,"placeholder":"e.g., Lahore, Karachi"},
                {"name":"class","label":"Select Class","type":"select","options":["Play Group","Nursery","Prep","1st Standard","2nd Standard","3rd Standard","4th Standard","5th Standard","6th Standard","7th Standard","8th Standard","9th (Science)","9th (Arts)","10th (Science)","10th (Arts)","F.Sc Pre-Med","F.Sc Pre-Eng","ICS","ICom","FA","B.Sc","B.Com","B.A"],"required":true}
            ],
            "submit_button_text": "Submit Application",
            "success_message": "Application submitted successfully! We will contact you soon."
        }'],
        ['section_id'=>21,'field_id'=>52,'value'=>'{
            "form_container": "bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto my-10",
            "label": "block text-sm font-medium text-gray-700 mb-1",
            "input": "mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500",
            "submit_button": "w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500",
            "title_style": "text-2xl font-bold text-center text-gray-800 mb-6"
        }'],

        ['section_id'=>22,'field_id'=>25,'value'=>'images/superiorlogo.jpg'],
        ['section_id'=>22,'field_id'=>26,'value'=>'We are committed to transform the lives of students, faculty and staff by providing them a Superior learning experience. Our plan EQ & IQ enables them to lead a meaningful & rewarding life.'],
        ['section_id'=>22,'field_id'=>27,'value'=>'042-111-00-00-78'],
        ['section_id'=>22,'field_id'=>28,'value'=>'info@superiorcolleges.edu.pk'],
        ['section_id'=>22,'field_id'=>29,'value'=>'The Superior Group of Colleges, 31 Tipu block New Garden Town Lahore'],
        ['section_id'=>22,'field_id'=>40,'value'=>'[
            {"platform":"facebook-f", "url":"https://facebook.com/superiorcollege"},
            {"platform":"instagram", "url":"https://instagram.com/superiorcollege"},
            {"platform":"linkedin", "url":"https://linkedin.com/school/superiorcollege"},
            {"platform":"youtube", "url":"https://youtube.com/superiorcollege"}
        ]'],
        ['section_id'=>22,'field_id'=>37,'value'=>'{
            "background_color": "#006400",
            "text_color": "#ffffff",
            "link_color": "#cccccc",
            "padding": "50px 20px 30px",
            "logo_width": "120px"
        }'],

        ['section_id'=>23,'field_id'=>30,'value'=>'© Copyright Superior Group All Rights Reserved 2026'],
        ['section_id'=>23,'field_id'=>38,'value'=>'{
            "background_color": "orange",
            "text_color": "#ffffff",
            "link_color": "#cccccc",
            "padding": "12px 20px",
            "alignment": "center",
            "font_size": "0.9rem"
        }'],

        // ===== CLASSES PAGE (Superior College) – General Classes with enhanced layout =====
        // Hero (section_id=24)
        ['section_id'=>24,'field_id'=>22,'value'=>'Lahore Main Campus'],
        ['section_id'=>24,'field_id'=>1,'value'=>'Our Classes & Programs'],
        ['section_id'=>24,'field_id'=>2,'value'=>'Choose the right path for your future'],
        ['section_id'=>24,'field_id'=>23,'value'=>'images/superiorlogo.jpg'],
        ['section_id'=>24,'field_id'=>3,'value'=>'images/superiorhero.jpg'],
        ['section_id'=>24,'field_id'=>32,'value'=>'{
            "overlay_color": "rgba(0, 100, 0, 0.5)",
            "blur": "8px",
            "text_align": "center",
            "text_position": "absolute",
            "text_top": "50%",
            "text_left": "50%",
            "transform": "translate(-50%, -50%)",
            "text_color": "#ffffff"
        }'],

        // About (section_id=25) – Introduction
        ['section_id'=>25,'field_id'=>49,'value'=>'{
            "blocks": [
                {
                    "type": "text",
                    "content": "Introduction"
                },
                {
                    "type": "text",
                    "content": "Superior College offers a wide range of classes from Early Years to Undergraduate programs. Our curriculum is designed to nurture young minds, build strong foundations, and prepare students for higher education and professional careers. With state-of-the-art facilities, experienced faculty, and a supportive learning environment, we ensure every student reaches their full potential."
                }
            ]
        }'],
        ['section_id'=>25,'field_id'=>33,'value'=>'{
            "padding": "40px",
            "background_color": "#ffffff",
            "text_color": "#333",
            "alignment": "left"
        }'],

        // Programs (section_id=26) – 6 cards (general class listing)
        ['section_id'=>26,'field_id'=>15,'value'=>'Available Classes'],
        ['section_id'=>26,'field_id'=>48,'value'=>'[
            {
                "title": "🎓 Early Years",
                "type": "list",
                "items": ["Play Group", "Nursery", "Prep"],
                "color": "#FF8C00"
            },
            {
                "title": "📚 Primary School",
                "type": "list",
                "items": ["1st Standard", "2nd Standard", "3rd Standard", "4th Standard", "5th Standard"],
                "color": "#006400"
            },
            {
                "title": "🏫 Middle School",
                "type": "list",
                "items": ["6th Standard", "7th Standard", "8th Standard"],
                "color": "#0033cc"
            },
            {
                "title": "🎓 Secondary School",
                "type": "grouped",
                "groups": {
                    "🔬 Science Group": ["9th (Science)", "10th (Science)"],
                    "🎨 Arts Group": ["9th (Arts)", "10th (Arts)"]
                },
                "color": "#FF8C00"
            },
            {
                "title": "📖 Higher Secondary",
                "type": "list",
                "items": ["F.Sc Pre-Med", "F.Sc Pre-Eng", "ICS", "ICom", "FA"],
                "color": "#006400"
            },
            {
                "title": "🎓 Undergraduate",
                "type": "list",
                "items": ["B.Sc", "B.Com", "B.A"],
                "color": "#0033cc"
            }
        ]'],
        ['section_id'=>26,'field_id'=>35,'value'=>'{
            "background_color": "#f8f9fa",
            "padding": "40px 20px",
            "alignment": "center",
            "card_border_radius": "12px",
            "card_shadow": "0 8px 20px rgba(0,0,0,0.1)"
        }'],

        // About (section_id=27) – Eligibility
        ['section_id'=>27,'field_id'=>49,'value'=>'{
            "blocks": [
                {
                    "type": "text",
                    "content": "Eligibility Criteria"
                },
                {
                    "type": "text",
                    "content": "Minimum age requirements apply per class level. For Early Years, child must be at least 3 years old. For Primary and above, previous class promotion certificate is required. For Higher Secondary, Matriculation or equivalent is mandatory. Please contact our admission office for detailed eligibility criteria."
                }
            ]
        }'],
        ['section_id'=>27,'field_id'=>33,'value'=>'{
            "padding": "40px",
            "background_color": "#ffffff",
            "text_color": "#333",
            "alignment": "left"
        }'],

        // Admission CTA (section_id=28) – same as home page
        ['section_id'=>28,'field_id'=>18,'value'=>'ADMISSIONS OPEN'],
        ['section_id'=>28,'field_id'=>19,'value'=>'Join Superior and be a part of progressing future'],
        ['section_id'=>28,'field_id'=>20,'value'=>'ENROLL HERE!'],
        ['section_id'=>28,'field_id'=>21,'value'=>'images/superioraddmision.jpg'],
        ['section_id'=>28,'field_id'=>34,'value'=>'{
            "padding": "80px 20px",
            "background_image": "images/superioraddmision.jpg",
            "background_size": "cover",
            "background_position": "center",
            "text_color": "#ffffff",
            "alignment": "center",
            "button_background": "#ffffff",
            "button_text_color": "#0033cc",
            "button_border_radius": "50px",
            "form_page_slug": "admission-form"
        }'],

        // FAQ Section (section_id=29) – general FAQs
        ['section_id'=>29,'field_id'=>53,'value'=>'Frequently Asked Questions'],
        ['section_id'=>29,'field_id'=>54,'value'=>'[
            {
                "question": "What is the age requirement for Play Group?",
                "answer": "Minimum age for Play Group is 3 years by March 31 of the admission year."
            },
            {
                "question": "What documents are required for admission?",
                "answer": "Birth certificate, previous school report card (if applicable), CNIC copy of parents, and passport-sized photographs."
            },
            {
                "question": "Is there an entrance test?",
                "answer": "Yes, for classes 1st and above, an entrance test is conducted to assess basic skills. For Higher Secondary, admission is based on Matric marks."
            },
            {
                "question": "What is the fee structure?",
                "answer": "Fee structure varies by class level. Please visit our admission office or call our helpline for detailed information."
            },
            {
                "question": "Does Superior College offer scholarships?",
                "answer": "Yes, merit-based and need-based scholarships are available for deserving students."
            }
        ]'],
        ['section_id'=>29,'field_id'=>55,'value'=>'{
            "section_container": "bg-gray-50 py-12 px-4",
            "title_style": "text-3xl font-bold text-center text-gray-800 mb-8",
            "question_style": "font-semibold text-gray-800",
            "answer_style": "text-gray-600 mt-2",
            "item_container": "bg-white rounded-lg shadow p-4 mb-4"
        }'],

        // Footer (section_id=30) – same as home page
        ['section_id'=>30,'field_id'=>25,'value'=>'images/superiorlogo.jpg'],
        ['section_id'=>30,'field_id'=>26,'value'=>'We are committed to transform the lives of students, faculty and staff by providing them a Superior learning experience. Our plan EQ & IQ enables them to lead a meaningful & rewarding life.'],
        ['section_id'=>30,'field_id'=>27,'value'=>'042-111-00-00-78'],
        ['section_id'=>30,'field_id'=>28,'value'=>'info@superiorcolleges.edu.pk'],
        ['section_id'=>30,'field_id'=>29,'value'=>'The Superior Group of Colleges, 31 Tipu block New Garden Town Lahore'],
        ['section_id'=>30,'field_id'=>40,'value'=>'[
            {"platform":"facebook-f", "url":"https://facebook.com/superiorcollege"},
            {"platform":"instagram", "url":"https://instagram.com/superiorcollege"},
            {"platform":"linkedin", "url":"https://linkedin.com/school/superiorcollege"},
            {"platform":"youtube", "url":"https://youtube.com/superiorcollege"}
        ]'],
        ['section_id'=>30,'field_id'=>37,'value'=>'{
            "background_color": "#006400",
            "text_color": "#ffffff",
            "link_color": "#cccccc",
            "padding": "50px 20px 30px",
            "logo_width": "120px"
        }'],

        // Copyright (section_id=31)
        ['section_id'=>31,'field_id'=>30,'value'=>'© Copyright Superior Group All Rights Reserved 2026'],
        ['section_id'=>31,'field_id'=>38,'value'=>'{
            "background_color": "orange",
            "text_color": "#ffffff",
            "link_color": "#cccccc",
            "padding": "12px 20px",
            "alignment": "center",
            "font_size": "0.9rem"
        }'],
    ];
}