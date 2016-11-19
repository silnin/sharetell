# sharetell
Tell a story. Together

Hi! This is a little webservice which allows you to create simple collaborative stories.<br/>
It isn't intended for anything artistic or elaborate. It's just for a laugh or two.

# WORK IN PROGRESS!
Mind that this is very far from complete at this moment.


# Live demo
You can try it out on <a href="http://sharetell.silnin.nl">sharetell.silnin.nl</a>

# Installation
I'll put up a proper instruction here some day.
(and a Dockerfile) <br/>

For now: It's a symfony 3 project, and it requires mysql, php (5.6 or 7) and a webserver to run.

- create a database 'sharetell'
- put the MySQL credentials in app/config/parameters.yml
- download composer and run: php composer.phar install
- set up your webserver as you would for any symfony project
  - For apache: don't forget to a2enmod rewrite
