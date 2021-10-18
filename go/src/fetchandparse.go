package main

import (
	"fmt"
	"github.com/thst71/parseplan/go/parser"
)

func main() {
	fmt.Println("Hello World")

	var p parser.Parser
	p.Greeting = "Jello"

	fmt.Printf("%s\n", p.Greeting)
}
