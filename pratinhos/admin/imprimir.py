import win32print
import win32ui

def imprimir_comanda(texto):
    # Coloque o nome EXATO da sua impressora aqui
    nome_impressora = win32print.GetDefaultPrinter() 

    # Cria a conexão com a impressora
    h_printer = win32print.OpenPrinter(nome_impressora)
    try:
        # Inicia o documento
        job = win32print.StartDocPrinter(h_printer, 1, ("Comanda Delivery", None, "RAW"))
        win32print.StartPagePrinter(h_printer)

        # Envia o texto (precisa ser em bytes)
        win32print.WritePrinter(h_printer, texto.encode('utf-8'))

        win32print.EndPagePrinter(h_printer)
        win32print.EndDocPrinter(h_printer)
    finally:
        win32print.ClosePrinter(h_printer)

# Teste manual
conteudo = """
========== PEDIDO #001 ==========
Cliente: Joao da Silva
Itens:
1x X-Burger ........ R$ 25,00
1x Coca-Cola ....... R$ 7,00
---------------------------------
TOTAL: R$ 32,00
=================================
\n\n\n\n
"""
imprimir_comanda(conteudo)