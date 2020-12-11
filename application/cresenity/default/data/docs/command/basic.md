# Cresenity Command

### cek status sekarang ada di domain mana
---
```
php cf status
```
maka akan tampil
```
Domain: yourein.domain

AppID: 001
AppCode: yourappcode
OrgID: 1
OrgCode: yourorgcode
```

### pindah dari domain sekarang ke domain tujuan
---
```
php cf domain:switch domain.name
```
