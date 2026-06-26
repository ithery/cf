# PHPCF - General

### version

Menampilkan versi CF yang sedang digunakan.

```
phpcf version
```

### about

Menampilkan informasi lengkap tentang aplikasi.

```
phpcf about
```

Untuk menampilkan section tertentu saja:

```
phpcf about --only=environment
```

### environment

Menampilkan environment framework yang aktif.

```
phpcf environment
```

### serve

Menjalankan aplikasi pada PHP built-in development server.

```
phpcf serve
```

### key:generate

Generate application key baru.

```
phpcf key:generate
```

### composer

Menjalankan composer command melalui phpcf.

```
phpcf composer {args}
```

### npm

Menjalankan npm command melalui phpcf.

```
phpcf npm {npmArgs}
```
