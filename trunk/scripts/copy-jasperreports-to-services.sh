cd ~/projects/i*educar/trunk/intranet/relatorios/jasperreports/
echo "Copiando arquivos jasper para services local"

path="/sites_media_root/services/reports/jasper/"
sudo mkdir -p /sites_media_root/services/reports/jasper/
sudo cp *.jasper $path

sudo chmod -R 777 /sites_media_root
