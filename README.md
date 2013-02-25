Open Spam Registry
==================

This project was initially created to help website owners battle spammers. The concept is very simple - Before a user signs up to a site, their email and/or ip address is checked against the OSR database. If the user is a known spammer, they would be denied registration. 

If a website owner find some spam on their website, they can submit the spammers info to the OSR database. 

This code is only an API and has no website front end. There are only a couple end points currently working (see /OSR/src/app.php). However, all the data models are completed and tested. 

This project was built on Silex, the PHP micro framework, and uses DynamoDB as the database. I stopped development on it because of lack of funds. DynamoDB is expensive :)
I figure it can be used as a nice tutorial for using Silex and DynamoDB.

To get it up and running, do the following:
1. Set up you DynamoDB account on Amazon
2. Edit /OSR/src/Osr/Models/Storage/Adapter.php and add your AWS credentials in the construct
3. Run tests
