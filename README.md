# PowerPlant
## Założenia
Elektrownia produkuje prąd o jakiejś mocy. Elektrownia składa się z 20 generatorów prądu.
Proszę przygotować bazę danych (wybrać między mongo a mysqlem) i przygotować logowanie tych danych.
Każdy generator będzie wysyłał za pomocą jakiegoś prostego API informacje:

    id generatora
    aktualna moc (w kW)
    czas pomiaru - uwaga czas pomiaru nie wystarczy w sekundach gdyż przewidziane jest że taki odczyt będzie przez api wysyłany np co pół sekundy i musimy mieć dokładne dane co do części sekundy)

Proszę przygotować proste API do zapisu danych.
Proszę przemyśleć wybór bazy danych i jej odpowiednią strukturę ze względu na bardzo dużą ilość danych i ewentualne problemy z wyszukiwaniem (przykładowo 20 generatorów i każdy dwa razy na sekundę wrzuca swoje dane).

Proszę przygotować dodatkowo aby raz na dobę szedł raport z informacją o średniej mocy wytworzonej przez każdy generator w ciągu godziny.
Czyli będzie 20 generatorów * 24 wyniki (bo tyle godzin w ciągu doby).

Uwaga: w raporcie wyniki podajemy w MW.


Przygotować jakąś metodę przy pomocy serwisów która umożliwi wyszukanie danych wg parametrów:

    id generatora
    data od
    data do
    i wykorzystać ją do wylistowania tych danych (uwzględniając stronicowanie)


Dodatkowo punktowane będzie przygotowanie narzędzia które wypełni dane 20 generatorów za cały rok 2019.
Maksymalna moc jednego generatora to 1 MW (dane z odczytów bieżących niech będą więc losowymi liczbami z tego przedziału).
