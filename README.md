# Borealis

This project has no affilation with Ellucian or the University of Manitoba.

Borealis is a project that fetches information from the University of Manitoba's Banner Self-Service software (Aurora) and displays it in a modern web format.

Borealis is comprised of two parts: the API and the frontend. The API is self contained in the `/api` folder. The ReactJS frontend are the other files and folders. The API can be run on it's own since it has no dependency on the frontend. In fact, the API can be reused in a completely different setting. The frontend however requires the API as noted below.

## Requirements
### API
- PHP >= 7.2
  - curl
  - mysql || sqlite3
  - xml

### Frontend
`//TODO: write me`

## Running
### API
The API does not need to be compiled or built to be run. Simply dropping the API in the root along with the built frontend is enough. If you are running the API as it is (without a frontend) placing it in a location accessable by the public will work. With that being said, it is HIGHLY recommended to setup the configuration file beforehand.

### API Configuration File
The API uses a configuration file. To setup this up, copy or rename `_config.php.sample` to `_config.php`.  
It is important to change the `tmp_directory` entry (and `file` under `sqlite` if using SQLite) to a directory that has restricted access or is not accessable by the world. Failure to do so will expose user tokens to the public.

### Frontend
`//TODO: write me`

## License
This project is licensed under GNU AGPLv3. Please review [the license](LICENSE) before using this software.

## Contributing
Although the project is unfinished, feel free to contribute. There is no contributing document but please follow the current project's code style (snake_case variables/functions, braces on the outside, etc).
