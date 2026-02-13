import win32print

def teste_rapido():
    # Tente colocar o nome que você vê no Painel de Controle aqui
    nome = "NOME_DA_SUA_IMPRESSORA_AQUI" 
    try:
        p = win32print.OpenPrinter(nome)
        print(f"Sucesso! Conectado a {nome}")
        win32print.ClosePrinter(p)
    except:
        print("Ainda não encontrei a impressora. Verifique o nome exato.")

teste_rapido()