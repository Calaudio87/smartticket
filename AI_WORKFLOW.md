# Bitácora de Trabajo con Inteligencia Artificial (AI_WORKFLOW.md)

Este documento detalla la estrategia de colaboración con modelos de Inteligencia Artificial para el desarrollo del MVP **SmartTicket Triage**, documentando el stack utilizado, los prompts iterativos aplicados y las decisiones de auditoría y refactorización humana necesarias para garantizar estándares de producción.

---

## 1. Stack de IA Utilizado

* **Modelo Principal:** Claude 3.5 Sonnet / Gemini Pro (diseño de arquitectura, contratos de interfaces y scaffolding de componentes).
* **Asistente de IDE:** GitHub Copilot (autocompletado contextual y generación de estructuras base para pruebas unitarias).
* **Entorno de Evaluación:** Shell Linux / PHP CLI para ejecución y validación manual de cada snippet sugerido.

---

## 2. Prompts Principales e Iterativos

En lugar de delegar el enunciado completo en una sola instrucción ("one-shot"), el desarrollo se dividió en iteraciones modulares:

### Prompt 1: Arquitectura y Desacoplamiento (Backend)
> *"Diseña la arquitectura en Laravel 11 para clasificar tickets entrantes usando el Patrón Strategy. Requiero una interfaz `TicketAnalyzerInterface` y una implementación inicial `MockTicketAnalyzer`. Muestra cómo registrar el binding en `AppServiceProvider` y cómo inyectar el contrato en un `TicketController@store` que valide los datos con un `FormRequest`."*
* **Objetivo:** Forzar una arquitectura desacoplada donde el controlador dependa de abstracciones y no de implementaciones concretas de proveedores de IA.

### Prompt 2: Reactividad y Rendimiento (Livewire 3)
> *"Genera un componente Livewire 3 (`TicketDashboard`) para el panel de agentes. Debe incluir búsqueda en tiempo real con debounce, filtros por estado y prioridad, y tabla paginada. Es requisito estricto evitar el problema de consultas N+1 al contar las notas asociadas a cada ticket."*
* **Objetivo:** Obtener la estructura base reactiva optimizando el consumo de base de datos desde la capa de consulta.

### Prompt 3: Resiliencia y Fallback ante Caídas de API
> *"Refactoriza la llamada al servicio `TicketAnalyzerInterface` en `TicketController`. Si el servicio externo (como OpenAI o un microservicio de análisis) arroja un timeout o un error HTTP 429/500, el sistema no debe fallar con Error 500 ni interrumpir la creación del ticket. Debe registrar el incidente en logs y asignar una clasificación predeterminada de contingencia."*
* **Objetivo:** Asegurar alta disponibilidad y resiliencia en la recepción de tickets de los usuarios.

---

## 3. Auditoría Humana y Refactorización Crítica

Durante el desarrollo no se aceptó el código generado de forma pasiva. Se identificaron vulnerabilidades, incompatibilidades con el motor de base de datos y defectos de experiencia de usuario (UX) generados por la IA:

### 1. Incompatibilidad de Búsqueda en PostgreSQL (`LIKE` vs `ILIKE`)
* **Propuesta de la IA:** La IA sugirió filtrar texto con `where('subject', 'like', "%{$this->search}%")`, asumiendo MySQL o SQLite.
* **Problema Detectado:** En PostgreSQL, el operador `LIKE` es estricto con mayúsculas y minúsculas (*case-sensitive*). Buscar "factura" no arrojaba tickets titulados "Factura no recibida".
* **Refactorización:** Se auditó la consulta para utilizar `ilike` en entornos PostgreSQL, garantizando búsquedas insensibles a mayúsculas y minúsculas sin penalizar el rendimiento.

### 2. Vulnerabilidad de Integridad en Livewire (`changeStatus`)
* **Propuesta de la IA:**
  ```php
  public function changeStatus($ticketId, $newStatus) {
      Ticket::where('id', $ticketId)->update(['status' => $newStatus]);
  }
  Problema Detectado: Los métodos públicos en Livewire son puntos de entrada expuestos a manipulación vía XHR desde el cliente. Un usuario malintencionado podía enviar cualquier cadena arbitraria en $newStatus, corrompiendo el estado del ticket en base de datos.

Refactorización: Se implementó una lista blanca (whitelist) estricta:

PHP
$allowed = ['open', 'in_progress', 'resolved'];
abort_unless(in_array($newStatus, $allowed), 403, 'Estado no válido.');
Ticket::where('id', $ticketId)->update(['status' => $newStatus]);
3. Defecto de UX en Paginación Reactiva (WithPagination)
   Propuesta de la IA: Implementó el trait WithPagination, pero omitió los hooks del ciclo de vida de Livewire.

Problema Detectado: Si el agente se encontraba en la página 3 de resultados y ejecutaba una búsqueda o cambiaba un filtro, la URL mantenía ?page=3. Esto provocaba que la tabla se mostrara vacía si la nueva consulta contenía menos de 3 páginas de resultados.

Refactorización: Se agregaron los métodos de reseteo de paginación correspondientes:

PHP
public function updatedSearch() { $this->resetPage(); }
public function updatedStatus() { $this->resetPage(); }
public function updatedPriority() { $this->resetPage(); }
4. Resiliencia y Disponibilidad en Ingesta de Tickets
   Propuesta de la IA: El controlador invocaba el analizador directamente sin bloques de control de excepciones.

Problema Detectado: Ante un eventual corte de servicio o fallo en la API externa de IA, el cliente final experimentaba una pantalla blanca / Error 500, perdiéndose el ticket del usuario.

Refactorización: Se encapsuló la llamada dentro de un bloque try/catch. En caso de excepción, se escribe en Log::error() el mensaje técnico y se asigna un estado seguro de contingencia (categoría: 'Otros', prioridad: 'Media', summary: 'Pendiente de triaje manual'), garantizando que el ticket siempre se almacene.

5. Soporte Multibyte para Texto en Español en el Mock Analyzer
   Propuesta de la IA: Uso de la función nativa strtolower() para evaluar palabras clave como "urgente" o "facturación".

Problema Detectado: strtolower() no procesa correctamente caracteres con tilde ni caracteres multibyte de UTF-8 en español (ej. "CRÍTICO" o "FACTURACIÓN").

Refactorización: Se reemplazó por mb_strtolower($texto, 'UTF-8') y se completaron las ramas condicionales para dar cobertura a las categorías Seguridad / Abuso y Otros.

4. Conclusión
   El uso de asistentes de IA aceleró la escritura de plantillas Blade y la definición de esquemas iniciales, pero la intervención humana crítica fue indispensable para:

Garantizar la seguridad contra inyecciones de datos en peticiones reactivas.

Asegurar compatibilidad estricta con el motor PostgreSQL.

Proteger la resiliencia del sistema ante fallos de dependencias externas.
