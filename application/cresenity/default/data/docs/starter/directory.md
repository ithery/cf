# Directory Structure

### Introduction
Struktur Default Cresenity Framework dapat mempunyai banyak aplikasi yang dapat berjalan dalam 1 framework.
anda tidak dapat bebas menentukan directory structure yang diinginkan kecuali berada pada subfolder struktur app direktori

### Root Directory Structure
```
--- / (Document Root)
    |---- application/ (Application Directory)
    |---- data/ (Data Directory)
    |---- logs/ (Logs Directory)
    |---- media/ (Media Directory)
    |      |---- css/ (CSS Asset Directory)
    |      |---- js/ (JS Asset Directory)
    |---- modules/ (Modules Directory)
    |---- resources/ (Resources Directory)
    |---- system/ (System Directory)
    |---- temp/ (Temp Directory)
    |---- tests/ (Tests Directory)
    |---- index.php (Index File)

```


directory `logs`, `resources` dan `temp` harus writeable

### Application Directory Structure
```
--- / (Application Root)
    |---- config/ (Config Directory)
    |---- controllers/ (Controllers Directory)
    |---- data/ (Data Directory)
    |---- i18n/ (Translation Directory)
    |---- libraries/ (Libraries Directory)
    |---- media/ (Media Directory)
    |      |---- css/ (CSS Directory)
    |      |---- js/ (JS Directory)
    |---- navs/ (Navs Directory)
    |---- tests/ (Tests Directory)
    |---- themes/ (Themes Directory)
    |---- views/ (Views Directory)
    |---- bootstrap.php (Bootstrap File)
    |---- env.php (Env File)

```
