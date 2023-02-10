## Getting started

# Before
- make sure you have installed **docker**
- make sure you have installed **symfony cli**
- make sure you have installed **composer**

put .env inside ./back folder (ask to Fantin to get it)

# Run the app

Use makefile

First step, up containers :
```bash
make up
```

Second step, install dependencies for backend :
```bash
make backend-install
```

Third step, init jwt keys :
```bash
make init-jwt-keys
```

Fourth step, update database :
```bash
make schema-update
```

Fifth step, load fixtures :
```bash
make fixtures
```

Then run backend :
```bash
make start
```

go to http://localhost:8000/api for API Platform, http://localhost:3000 for VueJS app and http://localhost:8081 for adminer to manage your database.

For next time, you can use this command :
```bash
make start
```

You get 3 users for test :
admin@gmail.com / ROLE_ADMIN / password : password
user@gmail.com / ROLE_USER / password : password 
notverified@gmail.com / ROLE_USER / password : password (not verified by confirmation email)

