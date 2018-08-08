--------------------------------------------------------------------------------------------------------
----------------------------------------CAMBIUM LIMS----------------------------------------------------
--------------------------------------------------------------------------------------------------------

| INTRO |
    The LIMS (Laboratory Information Management System) is, in essence, a web-based application intended to be used by Cambium staff to store and manage laboratory testing.
    
    The system is broken down as follows: client-side data entry, review and summary (frontend), server-side processing of client input with API calls to format data (backend), and database storage of tests & samples (database).
    
    The system will be initially desinged to handle four sample types, all related to concrete: Cylinders, Mortar Cubes, Grout Cylinders and Cores. The test being performed is compressive strength.
    
| TECHNOLOGIES USED |
    >Frontent: Basic web suite. HTML5, JavaScript, CSS3. No frameworks/extensions for the time being; didn't want to limit who could work on the system based on framework.
    >Backend: PHP 7, procedural.
    >External API: Device Magic RESTful API (for formating documents into .docx & .PDF).
    >Database: MySQL, PHPMyAdmin.
    >Hosting: Unix-based server, cPanel - hosted on GoDaddy.
    >File management: FileZilla for server access, otherwise local storage. Will likely move to GIT when necessary.
    >IDE: Brackets.
    >Authentication: MySQL database pass/user list, session cookies for access. 
    
| COMPONENTS BREAKDOWN |
    Frontend:
        Consists of HTML pages setup in a simple site. The intended structure/navigation is as follows:
            1. Dashboard              
            2. Initial Login
                >Concrete Cylinders
                >Mortar Cubes
                >Grout Cylinders
                >Cores
            3. Review
                >Concrete Cylinders
                >Mortar Cubes
                >Grout Cylinders
                >Cores
            4. Break Input
            5. Stats
        
        1. The Dashboard will have a list of "to review" as well as basic input/throughput stats. This is based on date/break date.
        
        2. Initial Login is the form filled out by admin staff that includes the set details and the each sample's details. This saves these to the database. Each sample type has its own page, to facilitate changes that may become necessary on an individual-test basis.
        
        3. Review is where the reviewer loads a set after a break to ensure accuracy of information. Sets can be loaded from the dashboard based on teh queue there as well. Changes made here will update the database. On submission, the reviewed data is also sent through the Device Magic API for processing into a .docx or .PDF as report for the client.
        
        4. Break Input is used by lab staff at terminals in the lab. These allow staff to input the break details of each sample they test. This page will include a queue and an input section. On submission, data updates the null fields for the samples in the database. This event triggers a queue update for the master queue on the dashboard (the reviewer needs to look over the new break).
        
        5. Stats is a lower-priority page that is used by admin/management to look at aggregate statistics. I.e tracking and graphing relevant information to see the 'big picture' in the lab.
    
    Backend:
        The backend consists of the following parts:
            1. Initial Login Handler
            2. Review Handler 
            3. Queue Handler
            4. Break Input Handler
            5. Stats Handler
        
        1. Handles all submissions (calculations, format date/time and place them in the database).
        
        2. Fetches database info to populate review form and updating the database on submission. Also packages and sends API requests.
        
        3. Periodically updates the review and break queues and sends to the appropriate pages.
        
        4. Recieve and updates samples as breaks are performed.
        
        5. Updates and formats stats as required. 
        
    Database:
        Consists of the following tables:
            1. Sets
            2. Samples
            3. Authetication
            4. Stats
        
        1. Stores each set's common details. Key is the set ID.
        
        2. Stores each samples's individual details. Key is the sample's ID which is the set ID plus the sample identifier (i.e. 1007A). Contains the set ID as a seperate column.
        
        3. Stores user names and passwords. Either username or numbered keys.
        
        4. Stores statistics - based on query date/time? Unfinished design.
        
| Future Expansion |
    The LIMS will need to be modifiable and extendable, eventually encompassing all lab activities. In general, the model presented should be fairly easy to exend upon - new tests can have additional page-handler pairs and necessary database tables.
    
    Future tests will require more advanced reporting including graphs and figures. These will likely require their own intermediary modules before API submission.
        
