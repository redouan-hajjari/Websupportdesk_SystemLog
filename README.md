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
