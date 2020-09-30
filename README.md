# Borealis (backend portion)

This project has no affilation with Ellucian or the University of Manitoba.

Borealis is an API used to fetch information from the University of Manitoba's Banner Self-Service software, Aurora. This API is one half of a larger project, but interest has faded/I got burnt out, so this is being released on it's own.

## Requirements

- PHP >= 7.2
  - curl
  - mysql || sqlite3
  - xml

## Running

This project does not need to be compiled or built to be run. Simply dropping the project in a location accessable to the public is enough. With that being said, it is HIGHLY recommended to setup the configuration file beforehand.

#### Configuration File

This project uses a configuration file. To setup this up, copy or rename `_config.php.sample` to `_config.php`.  
It is important to change the `tmp_directory` entry (and `file` under `sqlite` if using SQLite) to a directory that has restricted access or is not accessable by the world. Failure to do so will expose user tokens to the public.

## License

This project is licensed under GNU AGPLv3. Please review [the license](LICENSE) before using this software.

## Contributing

Although the project is unfinished, feel free to contribute. There is no contributing document but please follow the current project's code style (snake_case variables/functions, braces on the outside, etc).