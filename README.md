# E-waiter Web

## How to run the project

1. Clone the repository using one of the following commands:
    - For SSH: `git clone ssh://git@gitlab.primebitgames.com:2222/ewaiter/new-ewaiter.git new-ewaiter`,
    - For HTTPS: `git clone https://gitlab.primebitgames.com/ewaiter/new-ewaiter.git new-ewaiter`.
2. Navigate to your main folder after cloning it from repository by using `cd new-ewaiter`.
3. Make **init.sh** executable by using `sudo chmod +x init.sh`.
4. Run `make build` to build the project.
5. If you missed this step following the script, change your DNS server to `127.0.0.1`.

    - If you are using Windows, you can change your DNS server by
      following [this](https://www.windowscentral.com/how-change-your-pcs-dns-settings-windows-10) tutorial,
    - If you are using Linux, you can change your DNS server by
      following [this](https://www.linuxfordevices.com/tutorials/linux/change-dns-on-linux/) tutorial,
    - If you are using Mac, you can change your DNS server by
      following [this](https://support.apple.com/pl-pl/guide/mac-help/mh14127/mac) tutorial.

   **NOTE**: If you change DNS server, ewaiter-dns container is needed to be running to access the internet!
6. Open both sites and set the images:

- http://matryoshka.e-waiter.lan/admin/settings/edit/6
- http://habibi.e-waiter.lan/admin/settings/edit/6

7. Project is ready to use now.

- You can access it at http://e-waiter.lan/admin
- You can access phpmyadmin at http://e-waiter.lan:8080

  **NOTE** phpmyadmin does not run automatically. You need to run it manually by
  running `docker start ewaiter-phpmyadmin` command."

8. Enjoy ( ͡° ͜ʖ ͡°)

## How to get access from another device in the same network

1. Find your local IP address by using:

- `Get-NetIPAddress -PrefixOrigin Dhcp | Format-Table` in PowerShell on Windows,
- `ifconfig | grep 'inet ' | grep -v '127.0.0.1' | awk '{print $2}'` in Terminal on Linux/Mac.

2. Edit **vite.config.js** file in the main folder as follows:
    ```javascript
    server: {
            host: "0.0.0.0",
            hmr: {
                host: "your_local_ip_address",
            },
        },
    ```
   where `your_local_ip_address` is your local IP address from step 1.
3. Log in to **AdGuard Home** dashboard at http://e-waiter.lan:8123:
    - Username: `admin`,
    - Password: `asdASD123`.
4. Open **Filters** tab and click **DNS rewrites**.
5. Edit existing DNS rewrites from:

   | Domain          | Answer    |
       | --------------- | --------- |
   | e-waiter.lan    | 127.0.0.1 |
   | \*.e-waiter.lan | 127.0.0.1 |

   to:

   | Domain          | Answer                |
       | --------------- | --------------------- |
   | e-waiter.lan    | your_local_ip_address |
   | \*.e-waiter.lan | your_local_ip_address |

   where `your_local_ip_address` is your local IP address from step 1.

6. Enjoy ( ͡° ͜ʖ ͡°)

## How to acces swagger documentation

1. Open http://e-waiter.lan/api/docs in your browser.
2. Enjoy ( ͡° ͜ʖ ͡°)

### For updating the project docs 
Please remember update the ApiController Version in ```ApiController.php``` file in the ```app/Http/Controllers/Api/ApiController.php```.<br>
If docs not generate automatically after updating then run the following command
```bash
php artisan l5-swagger:generate
```
