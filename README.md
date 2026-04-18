# WebSupportDesk System Logs (Magento 2)

## Nederlands

Admin onder **System → Tools → System Logs**: alle `*.log` bestanden in `var/log` in een dropdown, **volledige inhoud** bekijken, en een log **leggen** (inhoud wissen).

Metadata (eerste gezien, laatst gezien, laatst geleegd) wordt opgeslagen in **`var/.websupportdesk_systemlogs.json`**. Als een bestand op schijf **verdwijnt** (hernoemd/verwijderd), verdwijnt de bijbehorende regel bij de volgende sync automatisch uit dit bestand.

**Aan/uit:** **Stores → Configuration → WebSupportDesk → System Logs → Enable System Logs viewer** (alleen *Default Config*). Uit = geen toegang tot de viewer/API.

**Package:** `websupportdesk/module-system-logs` · **Module:** `WebSupportDesk_SystemLogs`

### Installatie

```bash
bin/magento module:enable WebSupportDesk_SystemLogs
bin/magento setup:upgrade
bin/magento cache:flush
```

### Donatie

Vind je deze module handig? Een vrijwillige bijdrage wordt gewaardeerd.

- **IBAN:** `NL12 BUNQ 2150 2834 33`
- **Tenaamstelling:** `R.Hajjari`
- **Contact:** [redouanhajjari@gmail.com](mailto:redouanhajjari@gmail.com?subject=Donatie%20WebSupportDesk_SystemLogs)

---

## English

In the admin, **System → Tools → System Logs** lists every `*.log` file under `var/log` in a dropdown, loads the **entire file** for viewing, and lets you **empty** a log (truncate).

Metadata (first seen, last seen, last cleared) is stored in **`var/.websupportdesk_systemlogs.json`**. If a file **no longer exists** on disk, its entry is removed from that JSON on the next sync.

**Enable/disable:** **Stores → Configuration → WebSupportDesk → System Logs → Enable System Logs viewer** (*Default Config* only). When disabled, the viewer page and AJAX endpoints are blocked.

**Package:** `websupportdesk/module-system-logs` · **Module:** `WebSupportDesk_SystemLogs`

### Install

```bash
bin/magento module:enable WebSupportDesk_SystemLogs
bin/magento setup:upgrade
bin/magento cache:flush
```

### Donation

If you find this module useful, a voluntary contribution is appreciated.

- **IBAN:** `NL12 BUNQ 2150 2834 33`
- **Account holder:** `R.Hajjari`
- **Contact:** [redouanhajjari@gmail.com](mailto:redouanhajjari@gmail.com?subject=Donation%20WebSupportDesk_SystemLogs)

---

Package: `websupportdesk/module-system-logs` · License: OSL-3.0 / AFL-3.0 · Source: [github.com/redouan-hajjari/Websupportdesk_SystemLog](https://github.com/redouan-hajjari/Websupportdesk_SystemLog)
