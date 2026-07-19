class Calculator {

    private display: HTMLInputElement;

    constructor() {
        this.display = document.getElementById("display") as HTMLInputElement;

    this.display.addEventListener("keydown", (event: KeyboardEvent) => {
        if (event.key === "Enter") {
            this.calculate();
        }
    });
        this.display = document.getElementById("display") as HTMLInputElement;
    }

    appendValue(value: string): void {
        this.display.value += value;
    }

    clearDisplay(): void {
        this.display.value = "";
    }

    deleteLast(): void {
        this.display.value =
            this.display.value.slice(0, -1);
    }

    calculate(): void {
        try {
            this.display.value =
                eval(this.display.value).toString();
        } catch {
            this.display.value = "Error";
        }
    }

    square(): void {
        let value = Number(this.display.value);
        this.display.value = (value * value).toString();
    }

    sqrt(): void {
        let value = Number(this.display.value);
        this.display.value =
            Math.sqrt(value).toString();
    }

    sin(): void {
        let value = Number(this.display.value);
        this.display.value =
            Math.sin(value * Math.PI / 180).toString();
    }

    cos(): void {
        let value = Number(this.display.value);
        this.display.value =
            Math.cos(value * Math.PI / 180).toString();
    }

    tan(): void {
        let value = Number(this.display.value);
        this.display.value =
            Math.tan(value * Math.PI / 180).toString();
    }

    log(): void {
        let value = Number(this.display.value);
        this.display.value =
            Math.log10(value).toString();
    }

    ln(): void {
        let value = Number(this.display.value);
        this.display.value =
            Math.log(value).toString();
    }
}

const calc = new Calculator();