# Webserver to Host Images

This is a simple application that will automatically create thumbnails for display on a webpage, allowing for a preview of the photo. The photos can be downloaded from the site. Please create your own `/thumbnails` directory, if necessary. Set your own directory permissions and ownership, and configure vhosts and domains, in order for this to work correctly.

Running:
```bash
sudo chmod 755 /path/to/thumbnails
sudo chown <WEB_SERVER> /path/to/thumbnails
```
should fix any issues. In my instance <WEB_SERVER> is "www-data".