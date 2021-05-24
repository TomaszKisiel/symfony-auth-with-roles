# :lock_with_ink_pen: Symfony Auth with Roles

Symfony token-based API with user roles. Each user can register
new account and update current profile data. Admins can viewing, 
creating, updating and deleting users. 

## :rabbit: Quickstart

To start working with this project, clone it on your local.

```
git clone https://github.com/TomaszKisiel/symfony-auth-with-roles
```

Start docker service on your machine and issue the command.

```
docker-compose up -d --build
```

Install dependencies and migrate database.

```
docker-compose exec php composer install --ignore-platform-reqs
docker-compose exec php bin/console doctrine:migrations:migrate
```

Now the project is running on ```localhost``` port ```80```. Have fun!

## :memo: API documentation

There is Swagger documentation at ([http://localhost/api/docs](http://localhost/api/docs)) to help 
you use the API. If something is missing or the page is not loading properly, report the problem.

Thanks to swagger UI you can deal with this api without any external api tools like 
Postman or Insomnia.

| Method | Route | Parameters | Success response | Failure response
|-------------|---|---| :---: | :---: |
| GET | /api/docs | - | 200 | - |
| GET | /api/register | email, password, first_name, last_name, age | 201 | 400 |
| GET | /api/login | email, password | 200 | 401 |
| GET | /api/profile | - | 200 | 401 |
| PATCH | /api/profile | email, password, roles, first_name, last_name, age | 200 | 400, 401 |
| GET | /api/users | - | 200 | 401, 403 |
| GET | /api/users/{id} | id | 200 | 401, 403 |
| POST | /api/users | email, password, roles, first_name, last_name, age | 201 | 400,401,403 |
| PATCH | /api/users/{id} | id, email, password, roles, first_name, last_name, age | 200 | 400,401,403 |
| DELETE | /api/users/{id} | id | 200 | 401,403 |

## :sob: Troubleshooting

If you have a problem with the permissions of files created by
docker, you should first take over them and try again.

```
chown -R <your_user>:<your_group> .
```

To get access to admin-only routes you must first change your role 
via [/api/profile]() path with ```PATCH``` method. E.g:

```
curl -X PATCH "http://localhost/api/profile" -H "accept: application/json" -H "Authorization: Bearer <your_token>" -H "Content-Type: application/json" -d "{\"roles\":\"ROLE_ADMIN\"}"
```

## :clipboard: License

Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
