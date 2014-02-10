deploy
======

deploy scripts

Usage: deploy <command> [path]

Commands :
  deploy config
    Output current configuration.
  diff <path>
    Show files and folders in specified path on local that are not on remote or have different size or timestamp
  down <path>
    Download the specified path from remote to local
  downdb
    Dump remote db to file, download file and import into local db.
  dumplocaldb
    Dump the local db to local file
  dumpremotedb
    Dump the remote db to local file. Not on FTP mode.
  up <path>
    Upload the specified path from local to remote
  deploy remote <remote>
    Switch remote.
