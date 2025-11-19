# Script PowerShell pour lancer un serveur HTTP local
$port = 8000
$path = $PSScriptRoot

Write-Host "Démarrage du serveur HTTP sur le port $port..." -ForegroundColor Green
Write-Host "Le site sera accessible à: http://localhost:$port" -ForegroundColor Yellow
Write-Host "Appuyez sur Ctrl+C pour arrêter le serveur" -ForegroundColor Cyan
Write-Host ""

# Vérifier si le port est déjà utilisé
$portInUse = Get-NetTCPConnection -LocalPort $port -ErrorAction SilentlyContinue
if ($portInUse) {
    Write-Host "Le port $port est déjà utilisé. Tentative avec le port 8080..." -ForegroundColor Yellow
    $port = 8080
}

# Créer un listener HTTP simple
$listener = New-Object System.Net.HttpListener
$listener.Prefixes.Add("http://localhost:$port/")
$listener.Start()

Write-Host "Serveur démarré avec succès sur http://localhost:$port" -ForegroundColor Green
Write-Host ""

while ($listener.IsListening) {
    $context = $listener.GetContext()
    $request = $context.Request
    $response = $context.Response
    
    $localPath = $request.Url.LocalPath
    if ($localPath -eq "/") {
        $localPath = "/index.html"
    }
    
    $filePath = Join-Path $path $localPath.TrimStart('/')
    
    Write-Host "$(Get-Date -Format 'HH:mm:ss') - $($request.HttpMethod) $localPath" -ForegroundColor Gray
    
    if (Test-Path $filePath -PathType Leaf) {
        $content = [System.IO.File]::ReadAllBytes($filePath)
        $extension = [System.IO.Path]::GetExtension($filePath).ToLower()
        
        # Définir le type MIME
        $mimeTypes = @{
            '.html' = 'text/html; charset=utf-8'
            '.css'  = 'text/css'
            '.js'   = 'application/javascript'
            '.json' = 'application/json'
            '.png'  = 'image/png'
            '.jpg'  = 'image/jpeg'
            '.jpeg' = 'image/jpeg'
            '.gif'  = 'image/gif'
            '.svg'  = 'image/svg+xml'
            '.ico'  = 'image/x-icon'
            '.txt'  = 'text/plain'
        }
        
        $contentType = $mimeTypes[$extension]
        if (-not $contentType) {
            $contentType = 'application/octet-stream'
        }
        
        $response.ContentType = $contentType
        $response.ContentLength64 = $content.Length
        $response.StatusCode = 200
        $response.OutputStream.Write($content, 0, $content.Length)
    } else {
        $response.StatusCode = 404
        $notFound = [System.Text.Encoding]::UTF8.GetBytes("404 - Fichier non trouvé")
        $response.ContentLength64 = $notFound.Length
        $response.OutputStream.Write($notFound, 0, $notFound.Length)
    }
    
    $response.Close()
}

