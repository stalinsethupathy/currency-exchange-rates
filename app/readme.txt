To fetch exchange rates for the default currency (USD)
	docker-compose run php-cli php index.php
	
To fetch exchange rates for a specific currency (e.g., EUR, AUD)
	docker-compose run php-cli php index.php EUR
	docker-compose run php-cli php index.php AUD

If you want to clean up unused Docker containers while running the application
	docker-compose run --rm --remove-orphans php-cli php index.php

To run PHPUnit test cases:
	docker-compose run phpunit
