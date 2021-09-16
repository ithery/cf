# CF Command - Basic

### Status command

Karena CF bersifat multi aplikasi, maka jalannya command akan mengarah pada 1 aplikasi saja.
Untuk melakukan check CF Command ada berada pada domain mana, dapat menggunakan command `status`

```
php cf status
```

Maka akan tampil:

```
Domain: yourein.domain

AppID: 001
AppCode: yourappcode
OrgID: 1
OrgCode: yourorgcode
```

### Swith domain

Jika kita ingin berpindah aplikasi, maka dapat dilakukan dengan command `domain:switch`
```
php cf domain:switch domain.name
```
