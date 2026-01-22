<?php


require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class OdontogramaController 

{
    public function index()
    {
        $id_paciente = $_GET['id'] ?? null;
        if (!$id_paciente) {
            header('Location: ' . BASE_URL . '/pacientes');
            exit;
        }

        $modelo = new OdontogramaModel($this->db);
        $odontogramas = $modelo->listarPorPaciente($id_paciente);

        require APP_ROOT . '/views/odontograma/index.php';
    }

    public function guardar()
    {
        $modelo = new OdontogramaModel($this->db);
        $modelo->guardar($_POST);
        echo json_encode(['ok' => true]);
    }

    public function editar()
    {
        $modelo = new OdontogramaModel($this->db);
        $modelo->actualizar($_POST);
        echo json_encode(['ok' => true]);
    }

    public function eliminar()
    {
        $modelo = new OdontogramaModel($this->db);
        $modelo->eliminar($_GET['id']);
        echo json_encode(['ok' => true]);
    }

    public function pdf()
    {
        $id_paciente = $_GET['id'];

        $modelo = new OdontogramaModel($this->db);
        $datos = $modelo->listarPorPaciente($id_paciente)->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        require APP_ROOT . '/views/odontograma/pdf.php';
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("odontograma.pdf", ['Attachment' => false]);
    }
}
