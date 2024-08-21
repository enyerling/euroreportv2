
<div class="container">
        <h2>Resultados de la Evaluación</h2>
        <p>Hotel: {{ $hotelName }}</p>
        <p>Gerente: {{ $managerName }}</p>
        <p>Fecha: {{ $issueDate }}</p>
        
        <h4>Puntaje Total: {{ number_format($totalScore, 2) }}</h4>

        <div class="card">
            <div class="card-header">Progreso por Sistema</div>
            <div class="card-body">
                @foreach ($scoresBySystem as $systemId => $score)
                    <p>{{ $score['systemName'] }}: {{ number_format(($score['correctAnswers'] / $score['totalQuestions']) * 100, 2) }}%</p>
                @endforeach
            </div>
        </div>

        <div class="card">
            <div class="card-header">Detalles por Sistema</div>
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>Sistema</th>
                            <th>Preguntas Totales</th>
                            <th>Respuestas Correctas</th>
                            <th>Puntaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($scoresBySystem as $systemId => $score)
                        <tr>
                            <td>{{ $score['systemName'] }}</td>
                            <td>{{ $score['totalQuestions'] }}</td>
                            <td>{{ $score['correctAnswers'] }}</td>
                            <td>{{ number_format($score['score'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($observations->isNotEmpty())
        <div class="card">
            <div class="card-header">Observaciones</div>
            <div class="card-body">
                <ul>
                    @foreach($observations as $observation)
                        <li>{{ $observation->answer }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
<br>

