<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use Dompdf\Dompdf;
use Dompdf\Options;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::orderBy('created_at', 'desc')->get();
        return view('reservation.reservation', compact('reservations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:billet,hotel',
            'reference' => 'required|string|unique:reservations,reference',
            'date_document' => 'required|date',
            'clients' => 'required|string',
        ]);

        $reservation = Reservation::create([
            'type' => $request->type,
            'reference' => $request->reference,
            'date_document' => $request->date_document,
            'clients' => $request->clients,
            'destination' => $request->destination,
            'ville' => $request->ville,
            'compagnie' => $request->compagnie,
            'date_depart' => $request->date_depart,
            'date_retour' => $request->date_retour,
            'ref_reservation' => $request->ref_reservation,
            'voyageurs' => $request->voyageurs,
            'nom_hotel' => $request->nom_hotel,
            'adresse_hotel' => $request->adresse_hotel,
            'telephone_hotel' => $request->telephone_hotel,
            'email_hotel' => $request->email_hotel,
            'date_arrivee' => $request->date_arrivee,
            'date_depart_hotel' => $request->date_depart_hotel,
            'nuits' => $request->nuits,
            'type_appartement' => $request->type_appartement,
            'adultes' => $request->adultes,
            'enfants' => $request->enfants,
            'tarif_euro' => $request->tarif_euro,
            'tarif_fcfa' => $request->tarif_fcfa,
            'agent_name' => $request->agent_name,
            'agent_fonction' => $request->agent_fonction,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Réservation enregistrée avec succès',
            'reservation' => $reservation
        ]);
    }

    public function show($id)
    {
        $reservation = Reservation::findOrFail($id);
        return response()->json($reservation);
    }

    public function getList()
    {
        $reservations = Reservation::orderBy('created_at', 'desc')->get();
        return response()->json($reservations);
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Réservation supprimée'
        ]);
    }

    public function generateWord(Request $request)
    {
        $type = $request->input('type');
        $data = $request->all();

        if ($type === 'billet') {
            return $this->generateBilletWord($data);
        } else {
            return $this->generateHotelWord($data);
        }
    }

    public function generatePDF(Request $request)
    {
        $type = $request->input('type');
        $data = $request->all();

        // Générer d'abord le document Word
        if ($type === 'billet') {
            $wordPath = $this->generateBilletWordPath($data);
        } else {
            $wordPath = $this->generateHotelWordPath($data);
        }

        if (!$wordPath || !file_exists($wordPath)) {
            return response()->json(['error' => 'Erreur lors de la génération du document Word'], 500);
        }

        // Convertir Word en PDF avec LibreOffice
        $pdfPath = $this->convertWordToPdf($wordPath);

        if (!$pdfPath || !file_exists($pdfPath)) {
            // Nettoyer le fichier Word
            @unlink($wordPath);
            return response()->json(['error' => 'Erreur lors de la conversion en PDF. Vérifiez que LibreOffice est installé.'], 500);
        }

        // Nettoyer le fichier Word
        @unlink($wordPath);

        $fileName = ($type === 'billet' ? 'billet_' : 'hotel_') . ($data['reference'] ?? 'document') . '.pdf';

        return response()->download($pdfPath, $fileName)->deleteFileAfterSend(true);
    }

    private function convertWordToPdf($wordPath)
    {
        try {
            // Charger le document Word
            $phpWord = IOFactory::load($wordPath);

            // Convertir en HTML
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
            $htmlPath = preg_replace('/\.docx$/', '.html', $wordPath);
            $htmlWriter->save($htmlPath);

            // Lire le contenu HTML
            $htmlContent = file_get_contents($htmlPath);

            // Configurer DomPDF
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($htmlContent);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Sauvegarder le PDF
            $pdfPath = preg_replace('/\.docx$/', '.pdf', $wordPath);
            file_put_contents($pdfPath, $dompdf->output());

            // Nettoyer le fichier HTML temporaire
            @unlink($htmlPath);

            return file_exists($pdfPath) ? $pdfPath : null;

        } catch (\Exception $e) {
            \Log::error('Erreur conversion PDF: ' . $e->getMessage());
            return null;
        }
    }

    private function generateBilletWordPath($data)
    {
        $templatePath = resource_path('views/reservation/NEW ATTESTATION DE RESERVATION BILLET.docx');

        if (!file_exists($templatePath)) {
            return null;
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Remplacer les placeholders
        $templateProcessor->setValue('REFERENCE', $data['reference'] ?? '');
        $templateProcessor->setValue('DATE_DOCUMENT', $this->formatDate($data['date_document'] ?? ''));
        $templateProcessor->setValue('DESTINATION', $data['destination'] ?? '');
        $templateProcessor->setValue('VILLE', $data['ville'] ?? '');
        $templateProcessor->setValue('COMPAGNIE', $data['compagnie'] ?? '');
        $templateProcessor->setValue('DATE_DEPART', $this->formatDate($data['date_depart'] ?? ''));
        $templateProcessor->setValue('DATE_RETOUR', $this->formatDate($data['date_retour'] ?? ''));
        $templateProcessor->setValue('REF_RESERVATION', $data['ref_reservation'] ?? '');
        $templateProcessor->setValue('AGENT_NAME', $data['agent_name'] ?? '');
        $templateProcessor->setValue('AGENT_FONCTION', $data['agent_fonction'] ?? '');

        // Calculer la durée
        if (!empty($data['date_depart']) && !empty($data['date_retour'])) {
            $depart = Carbon::parse($data['date_depart']);
            $retour = Carbon::parse($data['date_retour']);
            $duree = $retour->diffInDays($depart);
            $templateProcessor->setValue('DUREE', $duree . ' jours');
        } else {
            $templateProcessor->setValue('DUREE', '');
        }

        // Traiter les voyageurs
        $voyageurs = json_decode($data['voyageurs'] ?? '[]', true);
        $validVoyageurs = array_filter($voyageurs, fn($v) => !empty($v['nom']));
        $validVoyageursArray = array_values($validVoyageurs);
        $nbVoyageurs = count($validVoyageursArray);

        $templateProcessor->cloneRow('VOYAGEUR1_NOM', $nbVoyageurs);

        for ($i = 1; $i <= $nbVoyageurs; $i++) {
            $voyageur = $validVoyageursArray[$i - 1];
            $templateProcessor->setValue("NUM#$i", $i);
            $templateProcessor->setValue("VOYAGEUR1_NOM#$i", $voyageur['nom'] ?? '');
            $templateProcessor->setValue("VOYAGEUR1_PASSEPORT#$i", $voyageur['passeport'] ?? '');
            $templateProcessor->setValue("VOYAGEUR1_LIEU#$i", $voyageur['lieu'] ?? $voyageur['pays'] ?? '');
            $templateProcessor->setValue("VOYAGEUR1_DELIVRANCE#$i", $this->formatDate($voyageur['delivrance'] ?? ''));
            $templateProcessor->setValue("VOYAGEUR1_EXPIRATION#$i", $this->formatDate($voyageur['expiration'] ?? ''));
        }

        $templateProcessor->setValue('NB_VOYAGEURS', $nbVoyageurs);

        // Générer le QR Code
        $qrData = "AUTHENTIFIER\nPSI AFRICA - Réservation Billet\nRéf: " . ($data['reference'] ?? '') . "\nDate: " . $this->formatDate($data['date_document'] ?? '') . "\nDocument Officiel";
        $qrCodePath = storage_path('app/temp/qr_' . ($data['reference'] ?? 'temp') . '.png');

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $this->generateQrCode($qrData, $qrCodePath);

        if (file_exists($qrCodePath)) {
            $templateProcessor->setImageValue('CODE_QR', [
                'path' => $qrCodePath,
                'width' => 80,
                'height' => 80,
                'ratio' => true
            ]);
        }

        $fileName = 'billet_' . ($data['reference'] ?? 'document') . '.docx';
        $tempPath = storage_path('app/temp/' . $fileName);

        $templateProcessor->saveAs($tempPath);

        // Nettoyer le QR code
        @unlink($qrCodePath);

        return $tempPath;
    }

    private function generateHotelWordPath($data)
    {
        $templatePath = resource_path('views/reservation/NEW RESERVATION HOTEL.docx');

        if (!file_exists($templatePath)) {
            return null;
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Formater les clients
        $clientsRaw = $data['clients'] ?? '';
        $clientsArray = array_filter(explode("\n", $clientsRaw));
        $clientsList = [];
        $i = 1;
        foreach ($clientsArray as $client) {
            $client = trim($client);
            if (!empty($client)) {
                $clientsList[] = $i . '. ' . strtoupper($client);
                $i++;
            }
        }
        $clientsFormatted = implode('</w:t><w:br/><w:t>', $clientsList);

        $templateProcessor->setValue('REFERENCE', $data['reference'] ?? '');
        $templateProcessor->setValue('DATE_DOCUMENT', $this->formatDate($data['date_document'] ?? ''));
        $templateProcessor->setValue('CLIENTS', $clientsFormatted);
        $templateProcessor->setValue('NOM_HOTEL', $data['nom_hotel'] ?? '');
        $templateProcessor->setValue('ADRESSE_HOTEL', $data['adresse_hotel'] ?? '');
        $templateProcessor->setValue('ADRESS_HOTEL', $data['adresse_hotel'] ?? '');
        $templateProcessor->setValue('TELEPHONE_HOTEL', $data['telephone_hotel'] ?? '');
        $templateProcessor->setValue('EMAIL_HOTEL', $data['email_hotel'] ?? '');
        $templateProcessor->setValue('DATE_ARRIVEE', $this->formatDate($data['date_arrivee'] ?? ''));
        $templateProcessor->setValue('DATE_DEPART_HOTEL', $this->formatDate($data['date_depart_hotel'] ?? ''));

        $nuits = $data['nuits'] ?? 0;
        if (empty($nuits) && !empty($data['date_arrivee']) && !empty($data['date_depart_hotel'])) {
            $arrivee = Carbon::parse($data['date_arrivee']);
            $depart = Carbon::parse($data['date_depart_hotel']);
            if ($depart > $arrivee) {
                $nuits = $depart->diffInDays($arrivee);
            }
        }
        $templateProcessor->setValue('NUITS', $nuits . ' nuits');
        $templateProcessor->setValue('TYPE_APPARTEMENT', $data['type_appartement'] ?? '');
        $templateProcessor->setValue('ADULTES', $data['adultes'] ?? '0');
        $templateProcessor->setValue('ENFANTS', $data['enfants'] ?? '0');
        $templateProcessor->setValue('TARIF_EURO', ($data['tarif_euro'] ?? '0') . ' €');
        $templateProcessor->setValue('TARIF_FCFA', number_format($data['tarif_fcfa'] ?? 0, 0, ',', ' ') . ' FCFA');
        $templateProcessor->setValue('AGENT_NAME', $data['agent_name'] ?? '');
        $templateProcessor->setValue('AGENT_FONCTION', $data['agent_fonction'] ?? '');
        $templateProcessor->setValue('PAYS_HOTEL', 'France');
        $templateProcessor->setValue('VILLE_HOTEL', 'Paris');

        // Générer le QR Code
        $qrData = "AUTHENTIFIER\nPSI AFRICA - Réservation Hôtel\nRéf: " . ($data['reference'] ?? '') . "\nDate: " . $this->formatDate($data['date_document'] ?? '') . "\nDocument Officiel";
        $qrCodePath = storage_path('app/temp/qr_hotel_' . ($data['reference'] ?? 'temp') . '.png');

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $this->generateQrCode($qrData, $qrCodePath);

        if (file_exists($qrCodePath)) {
            $templateProcessor->setImageValue('CODE_QR', [
                'path' => $qrCodePath,
                'width' => 80,
                'height' => 80,
                'ratio' => true
            ]);
        }

        $fileName = 'hotel_' . ($data['reference'] ?? 'document') . '.docx';
        $tempPath = storage_path('app/temp/' . $fileName);

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $templateProcessor->saveAs($tempPath);

        // Nettoyer le QR code
        @unlink($qrCodePath);

        return $tempPath;
    }

    private function generateBilletWord($data)
    {
        $templatePath = resource_path('views/reservation/NEW ATTESTATION DE RESERVATION BILLET.docx');

        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template billet non trouvé'], 404);
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Remplacer les placeholders
        $templateProcessor->setValue('REFERENCE', $data['reference'] ?? '');
        $templateProcessor->setValue('DATE_DOCUMENT', $this->formatDate($data['date_document'] ?? ''));
        $templateProcessor->setValue('DESTINATION', $data['destination'] ?? '');
        $templateProcessor->setValue('VILLE', $data['ville'] ?? '');
        $templateProcessor->setValue('COMPAGNIE', $data['compagnie'] ?? '');
        $templateProcessor->setValue('DATE_DEPART', $this->formatDate($data['date_depart'] ?? ''));
        $templateProcessor->setValue('DATE_RETOUR', $this->formatDate($data['date_retour'] ?? ''));
        $templateProcessor->setValue('REF_RESERVATION', $data['ref_reservation'] ?? '');
        $templateProcessor->setValue('AGENT_NAME', $data['agent_name'] ?? '');
        $templateProcessor->setValue('AGENT_FONCTION', $data['agent_fonction'] ?? '');

        // Calculer la durée
        if (!empty($data['date_depart']) && !empty($data['date_retour'])) {
            $depart = Carbon::parse($data['date_depart']);
            $retour = Carbon::parse($data['date_retour']);
            $duree = $retour->diffInDays($depart);
            $templateProcessor->setValue('DUREE', $duree . ' jours');
        } else {
            $templateProcessor->setValue('DUREE', '');
        }

        // Traiter les voyageurs
        $voyageurs = json_decode($data['voyageurs'] ?? '[]', true);
        $validVoyageurs = array_filter($voyageurs, fn($v) => !empty($v['nom']));
        $validVoyageursArray = array_values($validVoyageurs);
        $nbVoyageurs = count($validVoyageursArray);

        // Cloner la ligne du tableau pour chaque voyageur
        $templateProcessor->cloneRow('VOYAGEUR1_NOM', $nbVoyageurs);

        // Remplir chaque ligne clonée
        for ($i = 1; $i <= $nbVoyageurs; $i++) {
            $voyageur = $validVoyageursArray[$i - 1];
            $templateProcessor->setValue("NUM#$i", $i); // Numéro de ligne
            $templateProcessor->setValue("VOYAGEUR1_NOM#$i", $voyageur['nom'] ?? '');
            $templateProcessor->setValue("VOYAGEUR1_PASSEPORT#$i", $voyageur['passeport'] ?? '');
            $templateProcessor->setValue("VOYAGEUR1_LIEU#$i", $voyageur['lieu'] ?? $voyageur['pays'] ?? '');
            $templateProcessor->setValue("VOYAGEUR1_DELIVRANCE#$i", $this->formatDate($voyageur['delivrance'] ?? ''));
            $templateProcessor->setValue("VOYAGEUR1_EXPIRATION#$i", $this->formatDate($voyageur['expiration'] ?? ''));
        }

        $templateProcessor->setValue('NB_VOYAGEURS', $nbVoyageurs);

        // Générer le QR Code avec filigrane AUTHENTIFIER
        $qrData = "AUTHENTIFIER\nPSI AFRICA - Réservation Billet\nRéf: " . ($data['reference'] ?? '') . "\nDate: " . $this->formatDate($data['date_document'] ?? '') . "\nDocument Officiel";
        $qrCodePath = storage_path('app/temp/qr_' . ($data['reference'] ?? 'temp') . '.png');

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        // Générer le QR code
        $this->generateQrCode($qrData, $qrCodePath);

        // Insérer le QR code dans le document
        if (file_exists($qrCodePath)) {
            $templateProcessor->setImageValue('CODE_QR', [
                'path' => $qrCodePath,
                'width' => 80,
                'height' => 80,
                'ratio' => true
            ]);
        }

        // Sauvegarder le fichier
        $fileName = 'billet_' . ($data['reference'] ?? 'document') . '.docx';
        $tempPath = storage_path('app/temp/' . $fileName);

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $templateProcessor->saveAs($tempPath);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    private function generateHotelWord($data)
    {
        $templatePath = resource_path('views/reservation/NEW RESERVATION HOTEL.docx');

        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template hôtel non trouvé'], 404);
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Formater les clients avec numérotation
        $clientsRaw = $data['clients'] ?? '';
        $clientsArray = array_filter(explode("\n", $clientsRaw));
        $clientsList = [];
        $i = 1;
        foreach ($clientsArray as $client) {
            $client = trim($client);
            if (!empty($client)) {
                $clientsList[] = $i . '. ' . strtoupper($client);
                $i++;
            }
        }
        // Joindre avec des sauts de ligne XML pour Word
        $clientsFormatted = implode('</w:t><w:br/><w:t>', $clientsList);

        // Remplacer les placeholders
        $templateProcessor->setValue('REFERENCE', $data['reference'] ?? '');
        $templateProcessor->setValue('DATE_DOCUMENT', $this->formatDate($data['date_document'] ?? ''));
        $templateProcessor->setValue('CLIENTS', $clientsFormatted);
        $templateProcessor->setValue('NOM_HOTEL', $data['nom_hotel'] ?? '');
        $templateProcessor->setValue('ADRESSE_HOTEL', $data['adresse_hotel'] ?? '');
        $templateProcessor->setValue('ADRESS_HOTEL', $data['adresse_hotel'] ?? ''); // Typo dans template
        $templateProcessor->setValue('TELEPHONE_HOTEL', $data['telephone_hotel'] ?? '');
        $templateProcessor->setValue('EMAIL_HOTEL', $data['email_hotel'] ?? '');
        $templateProcessor->setValue('DATE_ARRIVEE', $this->formatDate($data['date_arrivee'] ?? ''));
        $templateProcessor->setValue('DATE_DEPART_HOTEL', $this->formatDate($data['date_depart_hotel'] ?? ''));

        // Calculer la durée du séjour
        $nuits = $data['nuits'] ?? 0;
        if (empty($nuits) && !empty($data['date_arrivee']) && !empty($data['date_depart_hotel'])) {
            $arrivee = Carbon::parse($data['date_arrivee']);
            $depart = Carbon::parse($data['date_depart_hotel']);
            if ($depart > $arrivee) {
                $nuits = $depart->diffInDays($arrivee);
            }
        }
        $templateProcessor->setValue('NUITS', $nuits . ' nuits');
        $templateProcessor->setValue('TYPE_APPARTEMENT', $data['type_appartement'] ?? '');
        $templateProcessor->setValue('ADULTES', $data['adultes'] ?? '0');
        $templateProcessor->setValue('ENFANTS', $data['enfants'] ?? '0');
        $templateProcessor->setValue('TARIF_EURO', ($data['tarif_euro'] ?? '0') . ' €');
        $templateProcessor->setValue('TARIF_FCFA', number_format($data['tarif_fcfa'] ?? 0, 0, ',', ' ') . ' FCFA');
        $templateProcessor->setValue('AGENT_NAME', $data['agent_name'] ?? '');
        $templateProcessor->setValue('AGENT_FONCTION', $data['agent_fonction'] ?? '');

        // Placeholders supplémentaires pour le template
        $templateProcessor->setValue('PAYS_HOTEL', 'France'); // Valeur par défaut
        $templateProcessor->setValue('VILLE_HOTEL', 'Paris'); // Valeur par défaut

        // Générer le QR Code avec filigrane AUTHENTIFIER
        $qrData = "AUTHENTIFIER\nPSI AFRICA - Réservation Hôtel\nRéf: " . ($data['reference'] ?? '') . "\nDate: " . $this->formatDate($data['date_document'] ?? '') . "\nDocument Officiel";
        $qrCodePath = storage_path('app/temp/qr_hotel_' . ($data['reference'] ?? 'temp') . '.png');

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        // Générer le QR code
        $this->generateQrCode($qrData, $qrCodePath);

        // Insérer le QR code dans le document
        if (file_exists($qrCodePath)) {
            $templateProcessor->setImageValue('CODE_QR', [
                'path' => $qrCodePath,
                'width' => 80,
                'height' => 80,
                'ratio' => true
            ]);
        }

        // Sauvegarder le fichier
        $fileName = 'hotel_' . ($data['reference'] ?? 'document') . '.docx';
        $tempPath = storage_path('app/temp/' . $fileName);

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $templateProcessor->saveAs($tempPath);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    private function formatDate($date)
    {
        if (empty($date)) return '';
        try {
            $carbonDate = Carbon::parse($date);
            $mois = [
                1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
                5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
                9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'
            ];
            return $carbonDate->day . ' ' . $mois[$carbonDate->month] . ' ' . $carbonDate->year;
        } catch (\Exception $e) {
            return $date;
        }
    }

    private function generateQrCode($data, $path)
    {
        // Utiliser QR Server API pour générer le QR code
        $size = 200;
        $encodedData = urlencode($data);
        $url = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encodedData}&ecc=H&margin=5";

        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'Mozilla/5.0'
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);

        $imageData = @file_get_contents($url, false, $context);

        if ($imageData !== false) {
            file_put_contents($path, $imageData);
        }
    }
}
