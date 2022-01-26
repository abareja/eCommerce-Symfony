# AB Sklep
Prosta platforma sklepowa oparta o framework Symfony w wersji 6.0.1

W celu uruchomienia aplikacji należy wykonać polecenie:
```
php -S 127.0.0.1:8080 -t public/
```

A następnie w przeglądarce internetowej przejść na adres [127.0.0.1:8080](http://127.0.0.1:8080)

W celu usunięcia pamięci cache należy wykonać polecenie:
```
php bin/console cache:clear --env=prod
```