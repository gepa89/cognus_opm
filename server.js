/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */


const express = require('express');
const multer = require('multer');
const path = require('path');
const app = express();

// Configuración de Multer (donde se guardan los archivos)
const storage = multer.diskStorage({
    destination: function (req, file, cb) {
        cb(null, 'uploads/');  // Carpeta donde se guardarán los archivos
    },
    filename: function (req, file, cb) {
        cb(null, Date.now() + path.extname(file.originalname));  // Nombre único para el archivo
    }
});

// Inicializamos Multer
const upload = multer({ storage: storage });

// Ruta para recibir los documentos
app.post('/upload_documents', upload.array('documents[]'), (req, res) => {
    const docCompra = req.body.doccompra;  // El valor 'doccompra' que se pasa desde el frontend
    const files = req.files;  // Archivos subidos

    // Verificamos si se subieron archivos
    if (!files || files.length === 0) {
        return res.status(400).send('No se cargaron archivos.');
    }

    // Guardamos la ruta de los archivos en la base de datos
    files.forEach(file => {
        const filePath = path.join('uploads', file.filename);
        console.log(`Archivo subido: ${filePath}`);  // Imprimir el nombre de archivo en la consola
    });

    // Responder con éxito
    res.send('Archivos subidos con éxito.');
});

// Servir archivos estáticos (para acceder a los archivos desde el navegador)
app.use('/uploads', express.static('uploads'));

// Iniciar el servidor en el puerto 3000
app.listen(3000, () => {
    console.log('Servidor corriendo en puerto 3000');
});
