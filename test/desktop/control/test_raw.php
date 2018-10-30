<?php

ini_set("session.save_handler", "redis");
ini_set("session.save_path", "tcp://127.0.0.1:6379?weight=1&persistent=1&prefix=PHPREDIS_SESSION_BANK_DEMO_&database=11");