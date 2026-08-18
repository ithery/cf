# PHPCF - Cron / Schedule

### cron:list

Lists the scheduled commands.

```
phpcf cron:list --timezone=
```

### cron:run

Runs every scheduled command that is due.

```
phpcf cron:run
```

### cron:work

Runs the schedule worker, which stays running and executes scheduled commands as they fall
due.

```
phpcf cron:work
```

### cron:test

Runs a single scheduled command, for testing purposes.

```
phpcf cron:test
```

### cron:finish

Handles the completion of a scheduled command.

```
phpcf cron:finish {id} {code}
```
