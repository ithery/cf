# PHPCF - Cron / Schedule

### cron:list

Menampilkan daftar scheduled commands.

```
phpcf cron:list --timezone=
```

### cron:run

Menjalankan semua scheduled commands yang sudah waktunya.

```
phpcf cron:run
```

### cron:work

Menjalankan schedule worker yang akan terus berjalan dan mengeksekusi scheduled commands.

```
phpcf cron:work
```

### cron:test

Menjalankan satu scheduled command untuk keperluan testing.

```
phpcf cron:test
```

### cron:finish

Handle penyelesaian scheduled command.

```
phpcf cron:finish {id} {code}
```
